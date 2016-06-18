<?php

// Enable Composer autoloader
/** @var \Composer\Autoload\ClassLoader $oAutoloader */
$oAutoloader = require dirname(__DIR__) . '/vendor/autoload.php';

// Register test classes
$oAutoloader->addPsr4('Asticode\CacheManager\Tests\\', __DIR__);

$oRedis = new \Asticode\CacheManager\Handler\RedisHandler([
    'servers' => '127.0.0.1:6379',
]);
$oRedis->test();
