<?php
namespace Asticode\CacheManager\Handler;

interface HandlerInterface
{

    public function get($sKey);
    public function set($sKey, $oDate, $iTTL);
    public function del($sKey);
}
