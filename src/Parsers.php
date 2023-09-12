<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

//function getParse(string $dataFile, string $extension): mixed
//{
//    switch ($extension) {
//        case 'json':
//            return json_decode($dataFile, true);
//        case 'yml':
//        case 'yaml':
//            return Yaml::parse($dataFile);
//        default:
//            throw new \Exception('Unknown extension ' . $extension);
//    }
//}

/**
 * @throws \Exception
 */
function getFileContent(string $pathToFile): string
{
    $contentOfFile = @file_get_contents($pathToFile);
    if ($contentOfFile !== false) {
        return $contentOfFile;
    }
    throw new \Exception("File not found", 1);
}

/**
 * @throws \Exception
 */
function parse(string $pathToFile)
{
    $contentOfFile = getFileContent($pathToFile);
    $extensionOfFile = pathinfo($pathToFile, PATHINFO_EXTENSION);
    return match ($extensionOfFile) {
        'json' => json_decode($contentOfFile, true),
        'yml', 'yaml' => Yaml::parse($contentOfFile),
        default => throw new \Exception("Unsupported format of incoming file!", 1),
    };
}
