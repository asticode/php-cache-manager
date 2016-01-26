<?php
namespace Asticode\CacheManager\Tests;

use Asticode\CacheManager\Handler\MemcachedHandler;
use PHPUnit_Framework_TestCase;

class BiteTest extends PHPUnit_Framework_TestCase
{

    function testBite()
    {
        $oBite = new MemcachedHandler([
            'host' => true,
            'port' => true,
        ]);
    }

}