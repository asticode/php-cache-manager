<?php
namespace Asticode\CacheManager\Handler;

use Asticode\Toolbox\ExtendedArray;
use RuntimeException;

class APCHandler extends AbstractHandler implements HandlerInterface
{
    // Constructor
    public function __construct(array $aConfig = [])
    {
        // Initialize
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

        // Check APC is loaded
        if (!extension_loaded('apc')) {
            throw new RuntimeException('APC extension is not loaded');
        }
    }

    public function get($sKey)
    {
        $bSuccess = false;
        $oData = apc_fetch($this->getKey($sKey), $bSuccess);
        return $bSuccess ? $oData : null;
    }

    public function set($sKey, $oData, $iTTL = -1)
    {
        return apc_store($this->getKey($sKey), $oData, $this->getTTL($iTTL));
    }

    public function del($sKey)
    {
        return apc_exists($this->getKey($sKey)) ? apc_delete($this->getKey($sKey)) : true;
    }
}
