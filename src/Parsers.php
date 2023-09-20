<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function getRealPath(string $pathToFile): string
{
    $fullPath = realpath($pathToFile);
    if ($fullPath === false) {
        throw new \Exception("File does not exists");
    }
    return $fullPath;
}

function getFormattedArray(mixed $dataArray): mixed
{
    return array_map(function ($value) {
        if ($value === false) {
            return 'false';
        } elseif ($value === true) {
            return 'true';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_array($value)) {
            return getFormattedArray($value); // Рекурсивный вызов для обработки вложенных массивов
        }
        return $value;
    }, $dataArray);
}

function getParseCode(string $pathToFile): mixed
{
    $fullPath = getRealPath($pathToFile);
    $data = file_get_contents($fullPath);
    $extension = pathinfo($fullPath)['extension'];
    $dataArray = [];

    if ($extension === 'yaml' || $extension === 'yml') {
        $dataArray = Yaml::parse($data);
    }
    if ($extension === 'json') {
        $dataArray = json_decode($data, true);
    }

    $resultArray = getFormattedArray($dataArray);

    return $resultArray;
}
