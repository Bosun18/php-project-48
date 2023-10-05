<?php

namespace Differ\Formatters\Stylish;

//function getStylish(array $diff, int $depth = 1, string $replacer = ' ', int $spaceCount = 4): string
//{
//    return iter($diff, $depth, $replacer, $spaceCount);
//}
//function normalize(mixed $data): array|string
//{
//    if (!is_array($data)) {
//        return match ($data) {
//            false => "false",
//            true => "true",
//            null => "null",
//            default => $data
//        };
//    }
//    return $data;
//}
//
//function iter(mixed $currentValue, int $depth, string $replacer, int $spaceCount): string
//{
//
//    $indentLength = $spaceCount * $depth;
//    $shift = 2;
//    $indent = str_repeat($replacer, $indentLength);
//    $indentStr = str_repeat($replacer, $indentLength - $shift);
//    $indentBrace = str_repeat($replacer, $indentLength - $spaceCount);
//
//    $str = array_map(
//        function ($item, $key) use ($spaceCount, $indent, $indentStr, $replacer, $depth) {
//            switch (true) {
//                case (!is_array($item)):
//                case (!array_key_exists('type', $item)):
//                    $normalizeValue = normalize($item);
//                    if (!is_array($normalizeValue)) {
//                        break;
//                    } else {
//                        return $indent . $key . ': ' . iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
//                    }
//                case ($item['type'] === 'added'):
//                    $normalizeValue = normalize($item['value']);
//                    if (!is_array($normalizeValue)) {
//                        break;
//                    } else {
//                        return $indentStr . '+ ' . $item['key'] . ': ' .
//                            iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
//                    }
//                case ($item['type'] === 'deleted'):
//                    $normalizeValue = normalize($item['value']);
//                    return $indentStr . '- ' . $item['key'] . ': ' .
//                        iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
//                case ($item['type'] === 'updated'):
//                    $normalizeValue1 = normalize($item['value']);
//                    $normalizeValue2 = normalize($item['value2']);
//                    return $indentStr . '- ' . $item['key'] . ': ' .
//                        iter($normalizeValue1, $depth + 1, $replacer, $spaceCount)
//                    . "\n" . $indentStr . '+ ' . $item['key'] . ': ' .
//                        iter($normalizeValue2, $depth + 1, $replacer, $spaceCount);
//                default:
//                    $normalizeValue = normalize($item['value']);
//                    if (is_array($normalizeValue)) {
//                        break;
//                    } else {
//                        return $indent . $item['key'] . ': ' .
//                            iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
//                    }
//            }
//        },
//        $currentValue,
//        array_keys($currentValue)
//    );
//    return implode("\n", ['{', ...$str, $indentBrace . '}']);
//}

function formatToStringFromDiffTree(array $diffTree, int $depth = 0): array
{
    $indent = buildIndent($depth);
    $depthOfDepth = $depth + 1;
    return array_map(function ($node) use ($indent, $depthOfDepth) {
        $key = $node['key'];
        $type = $node['type'];
        $value = $node['value'];

        switch ($type) {
            case 'nested':
                $nested = formatToStringFromDiffTree($value, $depthOfDepth);
                $stringifiedNest = implode("\n", $nested);
                return "{$indent}    {$key}: {\n{$stringifiedNest}\n{$indent}    }";
            case 'immutable':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}    {$key}: {$stringifiedValue}";
            case 'added':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}  + {$key}: {$stringifiedValue}";
            case 'deleted':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                return "{$indent}  - {$key}: {$stringifiedValue}";
            case 'updated':
                $stringifiedValue = valueToString($value, $depthOfDepth);
                $stringifiedValue2 = valueToString($node['value2'], $depthOfDepth);
                return "{$indent}  - {$key}: {$stringifiedValue}\n{$indent}  + {$key}: {$stringifiedValue2}";
            default:
                throw new \Exception("Unknown type - $type");
        }
    }, $diffTree);
}

function buildIndent(int $depth)
{
    return str_repeat("    ", $depth);
}

function valueToString(mixed $value, int $depth): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $result = convertArrayToString($value, $depth);
        $indent = buildIndent($depth);
        return "{{$result}\n{$indent}}";
    }
    return "$value";
}

function convertArrayToString(array $value, int $depth): string
{
    $keys = array_keys($value);
    $depthOfDepth = $depth + 1;

    return implode('', array_map(function ($key) use ($value, $depthOfDepth) {
        $newValue = valueToString($value[$key], $depthOfDepth);
        $indent = buildIndent($depthOfDepth);

        return "\n$indent$key: $newValue";
    }, $keys));
}


/**
 * @throws \Exception
 */
function getStylish(array $diffTree): string
{
    $formattedDiff = formatToStringFromDiffTree($diffTree);
    $result = implode("\n", $formattedDiff);

    return "{\n$result\n}";
}