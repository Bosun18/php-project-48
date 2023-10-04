<?php

namespace Differ\Formatters\Stylish;

function toString(string $value): string
{
    return trim(var_export($value, true), "'");
}

function getStylish(mixed $diff, int $depth = 1, string $replacer = ' ', int $spaceCount = 2): string
{
    return iter($diff, $depth, $replacer, $spaceCount);
}
function normalize(mixed $data): array|string
{
    if (!is_array($data)) {
        return match ($data) {
            false => "false",
            true => "true",
            null => "null",
            default => $data
        };
    }
    return $data;
}

function iter(mixed $currentValue, int $depth, string $replacer, int $spaceCount): string
{
    if (!is_array($currentValue)) {
        return normalize(toString($currentValue));
    }

    $indentLength = $spaceCount * $depth;
    $shift = 2;
    $indent = str_repeat($replacer, $indentLength);
    $indentStr = str_repeat($replacer, $indentLength - $shift);
    $indentBrace = str_repeat($replacer, $indentLength - $spaceCount);

    $str = array_map(
        function ($item, $key) use ($spaceCount, $indent, $indentStr, $replacer, $depth) {
            switch (true) {
                case (!is_array($item)):
                case (!array_key_exists('type', $item)):
                    $normalize = normalize($item);
                    return $indent . $key . ': ' . iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'added'):
                    $normalize = normalize($item['value']);
                    return $indentStr . '+ ' . $item['key'] . ': ' .
                iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'deleted'):
                    $normalize = normalize($item['value']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalize, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'updated'):
                    $normalize1 = normalize($item['value']);
                    $normalize2 = normalize($item['value2']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalize1, $depth + 1, $replacer, $spaceCount)
                    . "\n" . $indentStr . '+ ' . $item['key'] . ': ' .
                        iter($normalize2, $depth + 1, $replacer, $spaceCount);
                default:
                    $normalize = normalize($item['value']);
                    return $indent . $item['key'] . ': ' . iter($normalize, $depth + 1, $replacer, $spaceCount);
            }
        },
        $currentValue,
        array_keys($currentValue)
    );
    return implode("\n", ['{', ...$str, $indentBrace . '}']);
}
