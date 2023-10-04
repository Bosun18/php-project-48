<?php

namespace Differ\Phpunit\Tests\DiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    public function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
    /**
     * @throws \Exception
     */
    public function testStylishGenDiff(): void
    {
        $file1 = $this->getFixturePath('file5.json');
        $file2 = $this->getFixturePath('file6.json');
        $result = file_get_contents($this->getFixturePath('resultStylish.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'stylish'));

        $file1 = $this->getFixturePath('file7.yaml');
        $file2 = $this->getFixturePath('file8.yaml');
        $result = file_get_contents($this->getFixturePath('resultStylish.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'stylish'));
    }

    /**
     * @throws \Exception
     */
    public function testPlainGenDiff(): void
    {

        $file1 = $this->getFixturePath('file5.json');
        $file2 = $this->getFixturePath('file6.json');
        $result = file_get_contents($this->getFixturePath('resultPlain.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'plain'));

        $file1 = $this->getFixturePath('file7.yaml');
        $file2 = $this->getFixturePath('file8.yaml');
        $result = file_get_contents($this->getFixturePath('resultPlain.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'plain'));
    }

    /**
     * @throws \Exception
     */
    public function testJsonGenDiff(): void
    {
        $file1 = $this->getFixturePath('file5.json');
        $file2 = $this->getFixturePath('file6.json');
        $result = file_get_contents($this->getFixturePath('resultJson.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'json'));

        $file1 = $this->getFixturePath('file7.yaml');
        $file2 = $this->getFixturePath('file8.yaml');
        $result = file_get_contents($this->getFixturePath('resultJson.txt'));
        $this->assertEquals($result, genDiff($file1, $file2, 'json'));
    }
}
