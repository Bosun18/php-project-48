<?php

namespace Differ\Tests\DiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGenDiff()
    {
        $file5 = $this->getPathToFixture('file5.json');
        $file6 = $this->getPathToFixture('file6.json');
        $expected = file_get_contents($this->getPathToFixture('resultStilish.txt'));
        $this->assertEquals($expected, genDiff($file5, $file6, $format = 'stylish'));

        $file7 = $this->getPathToFixture('file7.yaml');
        $file8 = $this->getPathToFixture('file8.yaml');
        $expected = file_get_contents($this->getPathToFixture('resultStilish.txt'));
        $this->assertEquals($expected, genDiff($file7, $file8, $format = 'stylish'));

        $file1 = $this->getPathToFixture('file1.json');
        $file2 = $this->getPathToFixture('file2.json');
        $expected = file_get_contents($this->getPathToFixture('resultJson.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2, $format = 'stylish'));

        $file3 = $this->getPathToFixture('file3.yaml');
        $file4 = $this->getPathToFixture('file4.yaml');
        $expected = file_get_contents($this->getPathToFixture('resultJson.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2, $format = 'stylish'));
    }

    private function getPathToFixture($fixtureName): string
    {
        return "tests/fixtures/" . $fixtureName;
    }
}
