<?php
if (!file_exists($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    die('Unable to load vendor/autoload.php. Did you run composer install --dev?'.PHP_EOL);
}

$loader = require $autoloadFile;
$loader->addPsr4('Xabbuh\XApi\Client\Tests\\', __DIR__);
