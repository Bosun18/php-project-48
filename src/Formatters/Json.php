<?php

namespace Differ\Formatters\Json;

function getJsonFormat(array $diff): bool|string
{
    return json_encode($diff);
}
