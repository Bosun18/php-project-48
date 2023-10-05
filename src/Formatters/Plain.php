<?php

namespace Differ\Formatters\Plain;

function normalize(mixed $value): string|int|float
{
    if (!is_array($value)) {
        return match ($value) {
            false => "false",
            true => "true",
            null => "null",
            default => is_numeric($value) ? $value : "'$value'",
        };
    }
    return "[complex value]";
}

function getPlain(mixed $diff, string $keyName = ''): string
{
    $result = array_map(function ($currentValue) use ($keyName) {
        $type = $currentValue['type'];
        $key =  $currentValue['key'];
        $value = $currentValue['value'] ?? null;
        $newKey = $keyName === '' ? $key : "$keyName.$key";

        switch ($type) {
            case 'nested':
                return getPlain($value, $newKey);
            case 'added':
                $normalizeValue = normalize($value);
                return "Property '$newKey' was added with value: $normalizeValue";
            case 'deleted':
                return "Property '$newKey' was removed";
            case 'updated':
                $normalizeValue1 = normalize($value);
                $normalizeValue2 = normalize($currentValue['value2']);
                return "Property '$newKey' was updated. From $normalizeValue1 to $normalizeValue2";
            case 'immutable':
                break;
            default:
                throw new \Exception("Unknown node type: $type");
        }
    }, $diff);
    return implode("\n", array_filter($result));
}
