<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatter\getFormatter;

function getTree(mixed $data1, mixed $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($keys, SORT_REGULAR);

    $result = array_map(
        function ($key) use ($data1, $data2) {
            if (
                array_key_exists($key, $data1) && array_key_exists($key, $data2)
                && is_array($data1[$key]) && is_array($data2[$key])
            ) {
                $comparison = getTree($data1[$key], $data2[$key]);

                return [
                    'key' => $key,
                    'type' => 'nested',
                    'value1' => $comparison,
                    'value2' => $comparison,
                ];
            } elseif (!array_key_exists($key, $data2)) {
                return [
                    'key' => $key,
                    'type' => 'deleted',
                    'value1' => $data1[$key],
                    'value2' => null,
                ];
            } elseif (!array_key_exists($key, $data1)) {
                return  [
                    'key' => $key,
                    'type' => 'added',
                    'value1' => null,
                    'value2' => $data2[$key],
                ];
            } elseif ($data1[$key] !== $data2[$key]) {
                return  [
                    'key' => $key,
                    'type' => 'updated',
                    'value1' => $data1[$key],
                    'value2' => $data2[$key],
                ];
            } else {
                return [
                    'key' => $key,
                    'type' => 'immutable',
                    'value1' => $data1[$key],
                    'value2' => $data2[$key],
                ];
            }
        },
        $keys
    );
    return $result;
}

/**
 * @throws \Exception
 */
function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
}

function getResultValue(mixed $data): array
{
    return array_map(function ($value) {
        return match ($value) {
            false => 'false',
            true => 'true',
            null => 'null',
            default => is_array($value) ? getResultValue($value) : $value
        };
    }, $data);
}

/**
 * @throws \Exception
 */
function getParseData($pathToFile): array
{
    $extension = getExtension($pathToFile);
    $data = getData($pathToFile);
    return getResultValue(parse($data, $extension));
}

/**
 * @throws \Exception
 */
function getExtension($pathToFile): array|string
{
    $fullPath = getRealPath($pathToFile);
    return pathinfo($fullPath, PATHINFO_EXTENSION);
}

/**
 * @throws \Exception
 */
function getData(string $pathToFile): string|bool
{
    $fullPath = getRealPath($pathToFile);
    return file_get_contents($fullPath);
}

/**
 * @throws \Exception
 */
function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $data1 = getParseData($pathToFile1);
    $data2 = getParseData($pathToFile2);
    $diff = getTree($data1, $data2);

    return getFormatter($diff, $format) . "\n";
}
