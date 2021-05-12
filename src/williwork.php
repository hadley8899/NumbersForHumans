<?php

use RHDevelopment\Readable\Readable;

require __DIR__ . '/../vendor/autoload.php';

$test = Readable::getNumber(1);

var_dump($test);
