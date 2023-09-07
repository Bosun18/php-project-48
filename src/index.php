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
    //$args = Docopt::handle($doc);
    //echo $args . PHP_EOL;
    echo $doc . PHP_EOL;
}
