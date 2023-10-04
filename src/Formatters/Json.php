<?php

namespace Differ\Formatters\Json;

function getJson(array $diff): bool|string
{
    return json_encode($diff, JSON_PRETTY_PRINT);
}
