<?php

namespace Differ\Phpunit\Tests\DiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGenDiff()
    {
        $file1 = $this->fixturePath('file5.json');
        $file2 = $this->fixturePath('file6.json');
        $result = file_get_contents($this->fixturePath('resultStylish.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'stylish'));

        $file1 = $this->fixturePath('file7.yaml');
        $file2 = $this->fixturePath('file8.yaml');
        $result = file_get_contents($this->fixturePath('resultStylish.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'stylish'));

        $file1 = $this->fixturePath('file5.json');
        $file2 = $this->fixturePath('file6.json');
        $result = file_get_contents($this->fixturePath('resultPlain.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'plain'));

        $file1 = $this->fixturePath('file7.yaml');
        $file2 = $this->fixturePath('file8.yaml');
        $result = file_get_contents($this->fixturePath('resultPlain.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'plain'));
    }

    private function fixturePath($fixtureName): string
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
