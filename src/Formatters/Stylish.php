<?php

namespace Differ\Formatters\Stylish;

function getStylish(array $diff, int $depth = 1, string $replacer = ' ', int $spaceCount = 4): string
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
                    $normalizeValue = normalize($item);
                    if (!is_array($normalizeValue)) {
                        break;
                    } else {
                        return $indent . $key . ': ' . iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
                    }
                case ($item['type'] === 'added'):
                    $normalizeValue = normalize($item['value']);
                    if (!is_array($normalizeValue)) {
                        break;
                    } else {
                        return $indentStr . '+ ' . $item['key'] . ': ' .
                            iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
                    }
                case ($item['type'] === 'deleted'):
                    $normalizeValue = normalize($item['value']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
                case ($item['type'] === 'updated'):
                    $normalizeValue1 = normalize($item['value']);
                    $normalizeValue2 = normalize($item['value2']);
                    return $indentStr . '- ' . $item['key'] . ': ' .
                        iter($normalizeValue1, $depth + 1, $replacer, $spaceCount)
                    . "\n" . $indentStr . '+ ' . $item['key'] . ': ' .
                        iter($normalizeValue2, $depth + 1, $replacer, $spaceCount);
                default:
                    $normalizeValue = normalize($item['value']);
                    if (is_array($normalizeValue)) {
                        break;
                    } else {
                        return $indent . $item['key'] . ': ' .
                            iter($normalizeValue, $depth + 1, $replacer, $spaceCount);
                    }
            }
        },
        $currentValue,
        array_keys($currentValue)
    );
    return implode("\n", ['{', ...$str, $indentBrace . '}']);
}
