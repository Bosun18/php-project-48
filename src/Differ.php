<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\parse;
use function Differ\Formatter\format;

function buildTree(array $data1, array $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));

    return array_map(
        function ($key) use ($data1, $data2) {
            $value = $data1[$key] ?? null;
            $value2 = $data2[$key] ?? null;
            if (is_array($value) && is_array($value2)) {
                return [
                    'type' => 'nested',
                    'key' => $key,
                    'value' => buildTree($value, $value2),
                ];
            }
            if (!array_key_exists($key, $data2)) {
                return [
                    'type' => 'deleted',
                    'key' => $key,
                    'value' => $value,
                ];
            }
            if (!array_key_exists($key, $data1)) {
                return  [
                    'type' => 'added',
                    'key' => $key,
                    'value' => $value2,
                ];
            }
            if ($value !== $value2) {
                return  [
                    'type' => 'updated',
                    'key' => $key,
                    'value' => $value,
                    'value2' => $value2,
                ];
            }
            return [
                    'type' => 'immutable',
                    'key' => $key,
                    'value' => $value
                ];
        },
        $sortKeys
    );
}

/**
 * @throws \Exception
 */
function getFileData(string $pathToFile): string
{
    if (!file_exists($pathToFile)) {
        throw new \Exception("File not found");
    }
    return file_get_contents($pathToFile);
}

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $extension1 = pathinfo($pathToFile1, PATHINFO_EXTENSION);
    $extension2 = pathinfo($pathToFile2, PATHINFO_EXTENSION);
    $data1 = parse(getFileData($pathToFile1), $extension1);
    $data2 = parse(getFileData($pathToFile2), $extension2);

    $diff = buildTree($data1, $data2);

    return format($diff, $format);
}
