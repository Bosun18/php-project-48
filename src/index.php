<?php

namespace Index;

use Docopt;

function run()
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h | --help)
      gendiff (--version)

    Options:
      -h --help     Show this screen
      --version     Show version
    DOC;
    echo $doc . PHP_EOL;
}
