<?php
namespace Asticode\CacheManager\Handler;

use RuntimeException;

class APCHandler extends AbstractHandler implements HandlerInterface
{
    // Constructor
    public function __construct(array $aConfig = [])
    {
        // Parent construct
        parent::__construct($aConfig);

        // Check APC is loaded
        if (!extension_loaded('apc')) {
            throw new RuntimeException('APC extension is not loaded');
        }
    }

    public function get($sKey)
    {
        $bSuccess = false;
        $sValue = apc_fetch($this->getKey($sKey), $bSuccess);
        return $bSuccess ? $this->unserialize($sValue) : null;
    }

    public function set($sKey, $oData, $iTTL = -1)
    {
        return apc_store($this->getKey($sKey), $this->serialize($oData), $this->getTTL($iTTL));
    }

    public function del($sKey)
    {
        return apc_exists($this->getKey($sKey)) ? apc_delete($this->getKey($sKey)) : true;
    }
}
