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
        $sValue = $this->oClient->get($sKey);
        return $sValue ? $this->unserialize($sValue) : null;
    }

    public function set($sKey, $oData, $iTTL = -1)
    {
        return $this->oClient->set($this->getKey($sKey), $this->serialize($oData), $this->getTTL($iTTL));
    }

    public function del($sKey)
    {
        $bSuccess = $this->oClient->delete($this->getKey($sKey));
        return $this->oClient->getResultCode() !== Memcached::RES_NOTFOUND ? $bSuccess : true;
    }
}
