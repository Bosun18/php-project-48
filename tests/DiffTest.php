<?php

namespace Differ\Phpunit\Tests\DiffTest;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DiffTest extends TestCase
{
    public function getFixturePath(string $fixtureName): string
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }

    public static function additionProvider(): mixed
    {
        return [
            ['file5.json', 'file6.json', 'stylish', 'resultStylish'],
            ['file7.yaml', 'file8.yaml', 'stylish', 'resultStylish'],
            ['file5.json', 'file6.json', 'plain', 'resultPlain'],
            ['file7.yaml', 'file8.yaml', 'plain', 'resultPlain'],
            ['file5.json', 'file6.json', 'json', 'resultJson'],
            ['file7.yaml', 'file8.yaml', 'json', 'resultJson'],
        ];
    }
    /**
     * @throws Exception
     */
    #[DataProvider('additionProvider')]
    public function testGenDiff(string $file1, string $file2, string $format, string $expected): void
    {
        $fixture1 = $this->getFixturePath($file1);
        $fixture2 = $this->getFixturePath($file2);
        $result = $this->getFixturePath($expected);
        $this->assertStringEqualsFile($result, genDiff($fixture1, $fixture2, $format));
    }
}
