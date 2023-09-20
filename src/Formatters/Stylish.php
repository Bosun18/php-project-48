<?php

namespace Differ\Formatters\Stylish;

function toString($value): string
{
    return trim(var_export($value, true), "'") === "NULL"
        ? "null"
        : trim(var_export($value, true), "'");
}

function getFormat(mixed $value, string $shift = ' ', int $spaceCount = 4): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $iter = function ($currentItem, $depth) use (&$iter, $shift, $spaceCount) {

        if (!is_array($currentItem)) {
            return toString($currentItem);
        }

        $indentLength = $spaceCount * $depth;
        $shiftToLeft = 2;
        $shiftImmutableType = str_repeat($shift, $indentLength);
        $shiftMutableType = str_repeat($shift, $indentLength - $shiftToLeft);
        $bracketIndent = str_repeat($shift, $indentLength - $spaceCount);

        $strings = array_map(
            function ($item, $key) use ($shiftImmutableType, $shiftMutableType, $iter, $depth) {
                if (!is_array($item)) {
                    return $shiftImmutableType . $key . ': ' . $iter($item, $depth + 1);
                }
                if (!array_key_exists('type', $item)) {
                    return $shiftImmutableType . $key . ': ' . $iter($item, $depth + 1);
                }
                if ($item['type'] === 'added') {
                    return $shiftMutableType . '+ ' . $item['key'] . ': ' . $iter($item['value2'], $depth + 1);
                }
                if ($item['type'] === 'deleted') {
                    return $shiftMutableType . '- ' . $item['key'] . ': ' . $iter($item['value1'], $depth + 1);
                }
                if ($item['type'] === 'updated') {
                    return  $shiftMutableType . '- ' . $item['key'] . ': ' . $iter($item['value1'], $depth + 1) .
                        "\n" .  $shiftMutableType . '+ ' . $item['key'] . ': ' . $iter($item['value2'], $depth + 1);
                }
                return $shiftImmutableType . $item['key'] . ': ' . $iter($item['value1'], $depth + 1);
            },
            $currentItem,
            array_keys($currentItem)
        );
        $result = ['{', ...$strings, $bracketIndent . '}'];

        return implode("\n", $result);
    };
    return $iter($value, 1);
}
