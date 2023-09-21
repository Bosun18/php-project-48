<?php

namespace Differ\Phpunit\Tests\DifferTest;

use PHPUnit\Framework\TestCase;
use Differ\Differ;

class DifferTest extends TestCase
{
    public function testGenDiff()
    {
        $fixture1 = $this->getPathToFixture('file5.json');
        $fixture2 = $this->getPathToFixture('file6.json');
        $actual = Differ\genDiff($fixture1, $fixture2, 'stylish');
        $expected = file_get_contents($this->getPathToFixture('resultStylish.txt'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file7.yaml');
        $fixture2 = $this->getPathToFixture('file8.yaml');
        $actual = Differ\genDiff($fixture1, $fixture2, 'stylish');
        $expected = file_get_contents($this->getPathToFixture('resultStylish.txt'));
        $this->assertEquals($expected, $actual);
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
