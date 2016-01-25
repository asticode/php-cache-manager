<?php
namespace Asticode\CacheManager\Tests\Handler;

use Asticode\CacheManager\Handler\APCHandler;
use PHPUnit_Framework_TestCase;

class APCHandlerTest extends PHPUnit_Framework_TestCase
{

    function testComplete()
    {
        // Initialize
        $sKey = 'key_test';
        $sValue = 'value_test';
        $oAPC = new APCHandler();

        // Set
        $bSuccess = $oAPC->set($sKey, $sValue);
        $this->assertEquals(true, $bSuccess);

        // Get
        $sGet = $oAPC->get($sKey);
        $this->assertEquals($sValue, $sGet);

        // Delete
        $bSuccess = $oAPC->del($sKey);
        $this->assertEquals(true, $bSuccess);
        $sGet = $oAPC->get($sKey);
        $this->assertEquals(null, $sValue);
    }
}