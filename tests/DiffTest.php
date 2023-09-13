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
        $file1 = $this->getPathToFixture('file1.json');
        $file2 = $this->getPathToFixture('file2.json');
        $expected = file_get_contents($this->getPathToFixture('resultJson.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2));

        $file3 = $this->getPathToFixture('file3.yaml');
        $file4 = $this->getPathToFixture('file4.yaml');
        $expected = file_get_contents($this->getPathToFixture('resultJson.txt'));
        $this->assertEquals($expected, genDiff($file1, $file2));
    }

    private function getPathToFixture($fixtureName): string
    {
        return "tests/fixtures/" . $fixtureName;
    }
}
