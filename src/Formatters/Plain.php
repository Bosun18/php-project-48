<?php

namespace Differ\Formatters\Plain;

function normalize(mixed $value): string|int|float
{
    if (!is_array($value)) {
        if ($value === 'null' || $value === 'true' || $value === 'false') {
            return $value;
        }
        if (is_numeric($value)) {
            return $value;
        }
        return "'{$value}'";
    }
    return "[complex value]";
}


function getChange(mixed $diffArray, string $parentKey = ''): string
{
    $result = array_map(function ($node) use ($parentKey) {

        $type = $node['type'];
        $key =  $node['key'];
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        $newKey = $parentKey === '' ? $key : $parentKey . '.' . $key;

        switch ($type) {
            case 'nested':
                return getChange($value1, $newKey);
            case 'added':
                $normalize = normalize($value2);
                return "Property '" . $newKey . "' was added with value: " . $normalize;
            case 'deleted':
                return "Property '" . $newKey . "' was removed";
            case 'updated':
                $normalize1 = normalize($value1);
                $normalize2 = normalize($value2);
                return "Property '" . $newKey . "' was updated. From " . $normalize1 . ' to ' . $normalize2;
            case 'immutable':
                break;
            default:
                throw new \Exception("Unknown node type: {$type}");
        }
    }, $diffArray);
    $result = array_filter($result);

    return implode("\n", $result);
}
