<?php

namespace Differ\Formatters\Plain;

function normalize(mixed $value): string|int|float
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_string($value)) {
        return "'$value'";
    }
    if (is_array($value)) {
        return "[complex value]";
    }
    return $value;
}

function getPlain(array $diff, string $keyName = ''): string
{
    $result = array_map(function ($node) use ($keyName) {
        $type = $node['type'];
        $key =  $node['key'];
        $value = $node['value'] ?? null;
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
                $normalizeValue2 = normalize($node['value2']);
                return "Property '$newKey' was updated. From $normalizeValue1 to $normalizeValue2";
            case 'immutable':
                break;
            default:
                throw new \Exception("Unknown node type: $type");
        }
    }, $diff);
    return implode("\n", array_filter($result));
}
