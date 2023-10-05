<?php

namespace Differ\Formatters\Stylish;

use Exception;

/**
 * @throws Exception
 */
function getStylish(array $diff): string
{
    $result = implode("\n", iter($diff));
    return "{\n$result\n}";
}

function normalize(mixed $value, int $depth): string
{
    if (is_array($value)) {
        $result = makeString($value, $depth);
        $indent = makeIndent($depth);
        return "{{$result}\n$indent}";
    } else {
        return match ($value) {
            false => "false",
            true => "true",
            null => "null",
            default => "$value",
        };
    }
}

function iter(array $diff, int $depth = 0): array
{
    $indent = makeIndent($depth);
    $shift = $depth + 1;
    return array_map(function ($currentValue) use ($indent, $shift) {
        $key = $currentValue['key'];
        $type = $currentValue['type'];
        $value = $currentValue['value'];

        switch ($type) {
            case 'nested':
                $nested = iter($value, $shift);
                $normalizeValue = implode("\n", $nested);
                return "$indent    $key: {\n$normalizeValue\n$indent    }";
            case 'immutable':
                $normalizeValue = normalize($value, $shift);
                return "$indent    $key: $normalizeValue";
            case 'added':
                $normalizeValue = normalize($value, $shift);
                return "$indent  + $key: $normalizeValue";
            case 'deleted':
                $normalizeValue = normalize($value, $shift);
                return "$indent  - $key: $normalizeValue";
            case 'updated':
                $normalizeValue = normalize($value, $shift);
                $normalizeValue2 = normalize($currentValue['value2'], $shift);
                return "$indent  - $key: $normalizeValue\n$indent  + $key: $normalizeValue2";
            default:
                throw new Exception("Unknown type: $type");
        }
    }, $diff);
}

function makeIndent(int $depth): string
{
    return str_repeat("    ", $depth);
}


function makeString(array $value, int $depth): string
{
    $keys = array_keys($value);
    $shift = $depth + 1;

    return implode('', array_map(function ($key) use ($value, $shift) {
        $newValue = normalize($value[$key], $shift);
        $indent = makeIndent($shift);

        return "\n$indent$key: $newValue";
    }, $keys));
}
