<?php
namespace Asticode\CacheManager\Handler;

use Asticode\Toolbox\ExtendedArray;
use Memcached;
use RuntimeException;

class MemcachedHandler extends AbstractHandler implements HandlerInterface
{
    // Attributes
    /** @var \Memcached $oClient */
    private $oClient;

    // Constructor
    public function __construct(array $aConfig = [])
    {
        // Parent construct
        parent::__construct($aConfig);

        // Check required keys
        ExtendedArray::checkRequiredKeys(
            $aConfig,
            [
                'host',
                'port',
            ]
        );

        // Check Memcached is installed
        if (!class_exists('Memcached')) {
            throw new RuntimeException('Memcached is not installed');
        }

        // Create client
        $this->oClient = (new Memcached())
            ->addServer($aConfig['host'], $aConfig['port']);
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
