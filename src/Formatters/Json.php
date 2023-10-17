<?php

namespace Differ\Formatters\Json;

function getFormat(array $diff): bool|string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
