<?php

// Enable Composer autoloader
/** @var \Composer\Autoload\ClassLoader $oAutoloader */
$oAutoloader = require dirname(__DIR__) . '/vendor/autoload.php';

// Register test classes
$oAutoloader->addPsr4('Asticode\CacheManager\Tests\\', __DIR__);
