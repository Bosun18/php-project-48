<?php

namespace Differ\Tests\DiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    public function testGenDiff()
    {
//        $resultJson = file_get_contents(__DIR__ . "/fixtures/resultJson.txt");
//        $file1 = __DIR__ . "/fixtures/file1.json";
//        $file2 = __DIR__ . "/fixtures/file2.json";
//        $this->assertEquals($resultJson, genDiff($file1, $file2));

        $file1 = $this->getPathToFixture('file1.json');
        $file2 = $this->getPathToFixture('file2.json');
        $expected = file_get_contents($this->getPathToFixture('resultJson.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2));
    }

    private function getPathToFixture($fixtureName): string
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
