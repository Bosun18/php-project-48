<?php

namespace Differ\Formatters\Stylish;

use Exception;

/**
 * @throws Exception
 */
function getFormat(array $diff): string
{
    $result = implode("\n", iter($diff));
    return "{\n$result\n}";
}

function normalize(mixed $value, int $depth): string
{
    if (is_array($value)) {
        $result = makeString($value, $depth);
        $indent = makeIndent($depth);
        return "{{$result}\n{$indent}}";
    }
    return valueToString($value);
}

function valueToString(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    return "$value";
}

function iter(array $diff, int $depth = 0): array
{
    $indent = makeIndent($depth);
    $newDepth = $depth + 1;
    return array_map(function ($node) use ($indent, $newDepth) {
        $key = $node['key'];
        $type = $node['type'];
        $value = $node['value'] ?? null;

        switch ($type) {
            case 'nested':
                $nested = iter($value, $newDepth);
                $normalizedValue = implode("\n", $nested);
                return "$indent    $key: {\n$normalizedValue\n$indent    }";
            case 'immutable':
                $normalizedValue = normalize($value, $newDepth);
                return "$indent    $key: $normalizedValue";
            case 'added':
                $normalizedValue = normalize($value, $newDepth);
                return "$indent  + $key: $normalizedValue";
            case 'deleted':
                $normalizedValue = normalize($value, $newDepth);
                return "$indent  - $key: $normalizedValue";
            case 'updated':
                $normalizedValue = normalize($value, $newDepth);
                $normalizedValue2 = normalize($node['value2'], $newDepth);
                return "$indent  - $key: $normalizedValue\n$indent  + $key: $normalizedValue2";
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
    $newDepth = $depth + 1;

    return implode('', array_map(function ($key) use ($value, $newDepth) {
        $newValue = normalize($value[$key], $newDepth);
        $indent = makeIndent($newDepth);

        return "\n$indent$key: $newValue";
    }, $keys));
}
