<?php
namespace Asticode\CacheManager\Handler;

use Asticode\Toolbox\ExtendedArray;

abstract class AbstractHandler
{
    // Attributes

    protected $sPrefix;
    protected $iTTL;

    protected function __construct(array $aConfig)
    {
        // Extend config
        $aConfig = ExtendedArray::extendWithDefaultValues(
            $aConfig,
            [
                'prefix' => '',
                'ttl' => 0,
            ]
        );

        // Set attributes
        $this->sPrefix = $aConfig['prefix'];
        $this->iTTL = intval($aConfig['ttl']);
    }

    protected function buildTTL($iTTL)
    {
        if ($iTTL === -1) {
            return $this->iTTL;
        }
        return intval($iTTL);
    }

    protected function buildKey($sKey)
    {
        return $this->sPrefix . $sKey;
    }

    protected function serialize($oData)
    {
        return serialize($oData);
    }

    protected function unserialize($sValue)
    {
        return unserialize($sValue);
    }
}
