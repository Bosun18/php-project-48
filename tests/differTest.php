<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DiffTest extends TestCase
{
    public function testGenDiff()
    {
        $resultJson = file_get_contents(__DIR__ . "/fixtures/resultJson.txt");
        $file1 = __DIR__ . "/fixtures/file1.json";
        $file2 = __DIR__ . "/fixtures/file2.json";
        $this->assertEquals($resultJson, genDiff($file1, $file2));

        echo "\n\033[42mFlat Tests passed!]\033[0m]\n";
    }
}

