<?php

namespace Differ\Formatters\Json;

function getFormat(mixed $diffArray): string
{
    return json_encode($diffArray);
}
