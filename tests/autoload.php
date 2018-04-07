<?php

require_once __DIR__ . '/../vendor/autoload.php';

$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4("PhpMvcTest\\", __DIR__ . '/mvc', true);
$classLoader->register();