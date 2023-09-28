<?php

namespace Differ\Formatters\Stylish;

function toString(string $value): string
{
    return trim(var_export($value, true), "'");
}

function getStylish(mixed $diff, int $depth = 1, string $replacer = ' ', int $spaceCount = 4): string
{
    return iter($diff, $depth, $replacer, $spaceCount);
}

//function getResultValue(mixed $data): array|string
//{
//    return array_map(function ($value) {
//        return match ($value) {
//            false => 'false',
//            true => 'true',
//            null => 'null',
//            default => is_array($value) ? getResultValue($value) : $value
//        };
//    }, $data);
//}

function getResultValue(mixed $data): array|string
{
    if (!is_array($data)) {
        return match ($data) {
            false => 'false',
            true => 'true',
            null => 'null',
            default => is_numeric($data) ? $data : "{$data}"
        };
    }
    return $data;
}

function iter(mixed $currentValue, int $depth, string $replacer, int $spaceCount): string
{
    if (!is_array($currentValue)) {
        return toString($currentValue);
    }

    $indentLength = $spaceCount * $depth;
    $shift = 2;
    $indent = str_repeat($replacer, $indentLength);
    $indentStr = str_repeat($replacer, $indentLength - $shift);
    $indentBrace = str_repeat($replacer, $indentLength - $spaceCount);

    //    $str = array_map(
    //        function ($item, $key) use ($spaceCount, $indent, $indentStr, $replacer, $depth) {
    //            return match (true) {
    //                (!is_array($item)), (!array_key_exists('type', $item)) =>
    //                    $indent . $key . ': ' . iter($item, $depth + 1, $replacer, $spaceCount),
    //                ($item['type'] === 'added') =>
    //                    $indentStr . '+ ' . $item['key'] . ': ' .
    //                    iter($item['value2'], $depth + 1, $replacer, $spaceCount),
    //                ($item['type'] === 'deleted') =>
    //                    $indentStr . '- ' . $item['key'] . ': ' .
    //                    iter($item['value1'], $depth + 1, $replacer, $spaceCount),
    //                ($item['type'] === 'updated') =>
    //                    $indentStr . '- ' . $item['key'] . ': ' .
    //                    iter($item['value1'], $depth + 1, $replacer, $spaceCount) .
    //                "\n" . $indentStr . '+ ' . $item['key'] . ': ' .
    //                    iter($item['value2'], $depth + 1, $replacer, $spaceCount),
    //                default =>
    //                    $indent . $item['key'] . ': ' . iter($item['value1'], $depth + 1, $replacer, $spaceCount)
    //            };
    //        },
    //        $currentValue,
    //        array_keys($currentValue)
    //    );
    $str = array_map(
        function ($item, $key) use ($spaceCount, $indent, $indentStr, $replacer, $depth) {
            switch (true) {
                case (!is_array($item)):
                case (!array_key_exists('type', $item)):
                    $normalize = getResultValue($item);
                    return $indent . $key . ': ' . iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'added'):
                    $normalize = getResultValue($item['value2']);
                    return $indentStr . '+ ' . $item['key'] . ': ' .
                iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'deleted'):
                    $normalize = getResultValue($item['value1']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'updated'):
                    $normalize1 = getResultValue($item['value1']);
                    $normalize2 = getResultValue($item['value2']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalize1, $depth + 1, $replacer, $spaceCount)
                    . "\n" . $indentStr . '+ ' . $item['key'] . ': ' .
                        iter($normalize2, $depth + 1, $replacer, $spaceCount);
                default:
                    $normalize = getResultValue($item['value1']);
                    return $indent . $item['key'] . ': ' . iter($normalize, $depth + 1, $replacer, $spaceCount);
            }
        },
        $currentValue,
        array_keys($currentValue)
    );
    return implode("\n", ['{', ...$str, $indentBrace . '}']);
}
