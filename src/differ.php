<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;

function getData(string $data): array
{
    $dataDecode = json_decode($data, true);

    return array_map(function ($value) {
        if ($value === false) {
            return 'false';
        } elseif ($value === true) {
            return 'true';
        } elseif (is_null($value)) {
            return 'null';
        }
        return $value;
    }, $dataDecode);
}

function genDiff(string $firstFilePath, string $secondFilePath): string
{
//    $data1 = file_get_contents($firstFilePath);
//    $data2 = file_get_contents($secondFilePath);

    $data1 = parse($firstFilePath);
    $data2 = parse($secondFilePath);

    $dataArray1 = getData($data1);
    $dataArray2 = getData($data2);

    $keys = array_unique(array_merge(array_keys($dataArray1), array_keys($dataArray2)));

    sort($keys, SORT_REGULAR);

    $diffsFile = array_map(function ($key) use ($dataArray1, $dataArray2) {
        if (!array_key_exists($key, $dataArray1)) {
            return "  + $key: $dataArray2[$key]";
        }
        if (!array_key_exists($key, $dataArray2)) {
            return "  - $key: $dataArray1[$key]";
        }
        if ($dataArray1[$key] === $dataArray2[$key]) {
            return "    $key: $dataArray1[$key]";
        }
        return "  - $key: $dataArray1[$key]\n  + $key: $dataArray2[$key]";
    }, $keys);

    return "{\n" . implode("\n", $diffsFile) . "\n}";
}
