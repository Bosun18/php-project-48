<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;

//use function Functional\sort;
//use function Differ\Formatters\getFormatter;

function findDifferences($data1, $data2, $depth = 0): array
{
    $diff = [];

    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    sort($allKeys);

    foreach ($allKeys as $key) {
        if (isset($data1[$key]) && isset($data2[$key]) && is_array($data1[$key]) && is_array($data2[$key])) {
            $nestedDiff = findDifferences($data1[$key], $data2[$key], $depth + 1);
            if (!empty($nestedDiff)) {
                $diff['    ' . $key] = $nestedDiff;
            }
        } elseif (!array_key_exists($key, $data1)) {
            $diff['  + ' . $key] = $data2[$key];
        } elseif (!array_key_exists($key, $data2)) {
            $diff['  - ' . $key] = $data1[$key];
        } elseif ($data1[$key] !== $data2[$key]) {
            $diff['  - ' . $key] = $data1[$key];
            $diff['  + ' . $key] = $data2[$key];
        } else {
            $diff['    ' . $key] = $data1[$key];
        }
    }

    return $diff;
}

function getFormattedDiff($diff, $depth = 0): string
{
    $indent = str_repeat(' ', $depth * 4);
    $closeIndent = str_repeat(' ', $depth * 4 + 4);
    $output = "";

    foreach ($diff as $key => $value) {
        if (is_array($value)) {
            $output .= formatKey($key) . ": {\n";
            $output .= getFormattedDiff($value, $depth + 1);
            $output .= $closeIndent . "}\n";
        } elseif ($value === '') {
            $output .= $indent . formatKey($key) . ":" . "\n";
        } else {
            $output .= $indent . formatKey($key) . ": " . formatValue($value) . "\n";
        }
    }
    return $output;
}

function formatValue($value): string
{
    if ($value === null) {
        return 'null';
    }
    return trim(var_export($value, true), "'");
}

function formatKey($key)
{
    if (str_starts_with($key, '  + ')) {
        return $key;
    } elseif (str_starts_with($key, '  - ')) {
        return $key;
    } elseif (str_starts_with($key, '    ')) {
        return $key;
    }

    return '    ' . $key;
}

/**
 * @throws \Exception
 */
function genDiff($pathToFile1, $pathToFile2, $format = 'stilish'): string
{
    $data1 = parse($pathToFile1);
    $data2 = parse($pathToFile2);
    $diff = findDifferences($data1, $data2);
    $output = "{\n" . getFormattedDiff($diff) . "}\n";
    return $output;
}

/**
 * @throws \Exception
 */
//function getData($data): array
//{
//    return array_map(function ($value) use ($data) {
//        if ($value === false) {
//            return 'false';
//        } elseif ($value === true) {
//            return 'true';
//        } elseif (is_null($value)) {
//            return 'null';
//        } elseif (is_array($value)) {
//            return getData($value);
//        }
//        return $value;
//    }, $data);
//}
//
///**
// * @throws \Exception
// */
//function genDiff(string $firstFilePath, string $secondFilePath, string $formatName = 'stylish'): string
//{
//    $data1 = parse($firstFilePath);
//    $data2 = parse($secondFilePath);
//
//    $dataArray1 = getData($data1);
//    $dataArray2 = getData($data2);
//
//    $keys = array_unique(array_merge(array_keys($dataArray1), array_keys($dataArray2)));
//
//    sort($keys, SORT_REGULAR);
//
//    $diffsFile = array_map(function ($key) use ($dataArray1, $dataArray2) {
//        if (!array_key_exists($key, $dataArray1)) {
//            return "  + $key: $dataArray2[$key]";
//        }
//        if (!array_key_exists($key, $dataArray2)) {
//            return "  - $key: $dataArray1[$key]";
//        }
//        if ($dataArray1[$key] === $dataArray2[$key]) {
//            return "    $key: $dataArray1[$key]";
//        }
//        return "  - $key: $dataArray1[$key]\n  + $key: $dataArray2[$key]";
//    }, $keys);
//
//    return "{\n" . implode("\n", $diffsFile) . "\n}";
//}
