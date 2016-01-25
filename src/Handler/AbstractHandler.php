<?php
namespace Asticode\CacheManager\Handler;

abstract class AbstractHandler
{
    // Attributes
    protected $sPrefix;
    protected $iTTL;

    protected function getTTL($iTTL)
    {
        if ($iTTL === -1) {
            return $this->iTTL;
        }
        return intval($iTTL);
    }

    protected function getKey($sKey)
    {
        return $this->sPrefix . $sKey;
    }
}
