<?php

namespace Index;

use Docopt;

function run()
{
    $doc = <<<DOC
    Generate diff

    Usage:
     gendiff (-h|--help)
     gendiff (--version)
     gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen
      --version                     Show version
      --format <fmt>                Report format [default: stylish]
    DOC;
    //$args = Docopt::handle($doc);
    //echo $args . PHP_EOL;
    //echo $doc . PHP_EOL;
    $args = Docopt::handle($doc, array('version'=>'Gendiff 1.0'));
    foreach ($args as $k=>$v) (
	    echo $k.': '.json_encode($v).PHP_EOL;
    }
}
