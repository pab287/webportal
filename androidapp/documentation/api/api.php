<?php

require __DIR__ . '/../../vendor/autoload.php';

use OpenApi\Generator;

$openapi = Generator::scan([__DIR__ . '/controller']);

header('Content-Type: application/json');

echo $openapi->toJSON();
