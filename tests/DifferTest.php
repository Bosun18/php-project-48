<?php

namespace Differ\Phpunit\Tests\DifferTest;

use PHPUnit\Framework\TestCase;
use Differ\Differ;

class DifferTest extends TestCase
{
    public function testGenDiff(): void
    {
        $pathToFile1 = 'tests/fixtures/file1.json';
        $pathToFile2 = 'tests/fixtures/file2.json';
        $result1 = '{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
';
        $this->assertEquals($result1, Differ\genDiff($pathToFile1, $pathToFile2));

        $pathToFile3 = 'tests/fixtures/file3.yaml';
        $pathToFile4 = 'tests/fixtures/file4.yaml';
        $result2 = '{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
';
        $this->assertEquals($result2, Differ\genDiff($pathToFile3, $pathToFile4));

        $pathToFile5 = 'tests/fixtures/file5.json';
        $pathToFile6 = 'tests/fixtures/file6.json';
        $result3 = '{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}
';
        $this->assertEquals($result3, Differ\genDiff($pathToFile5, $pathToFile6));

        $pathToFile7 = 'tests/fixtures/file7.yaml';
        $pathToFile8 = 'tests/fixtures/file8.yaml';
        $result4 = '{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}
';
        $this->assertEquals($result4, Differ\genDiff($pathToFile7, $pathToFile8));
    }
}
