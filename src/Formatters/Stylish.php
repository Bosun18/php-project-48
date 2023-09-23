<?php

namespace Differ\Formatters\Stylish;

function toString(string $value): string
{
    return trim(var_export($value, true), "'");
}

function getStylish(mixed $diff, string $replacer = ' ', int $spaceCount = 4): string
{
    if (!is_array($diff)) {
        return toString($diff);
    }

    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spaceCount) {

        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentLength = $spaceCount * $depth;
        $shift = 2;
        $indent = str_repeat($replacer, $indentLength);
        $indentStr = str_repeat($replacer, $indentLength - $shift);
        $indentBrace = str_repeat($replacer, $indentLength - $spaceCount);

        $str = array_map(
            function ($item, $key) use ($indent, $indentStr, $iter, $depth) {
                return match (true) {
                    (!is_array($item)), (!array_key_exists('type', $item)) =>
                        $indent . $key . ': ' . $iter($item, $depth + 1),
                    ($item['type'] === 'added') =>
                        $indentStr . '+ ' . $item['key'] . ': ' . $iter($item['value2'], $depth + 1),
                    ($item['type'] === 'deleted') =>
                        $indentStr . '- ' . $item['key'] . ': ' . $iter($item['value1'], $depth + 1),
                    ($item['type'] === 'updated') =>
                        $indentStr . '- ' . $item['key'] . ': ' . $iter($item['value1'], $depth + 1) . "\n" .
                        $indentStr . '+ ' . $item['key'] . ': ' . $iter($item['value2'], $depth + 1),
                    default =>
                        $indent . $item['key'] . ': ' . $iter($item['value1'], $depth + 1)
                };
            },
            $currentValue,
            array_keys($currentValue)
        );
        return implode("\n", ['{', ...$str, $indentBrace . '}']);
    };
    return $iter($diff, 1);
}
