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
                'servers',
            ]
        );

        // Check Memcached is loaded
        if (!extension_loaded('memcached')) {
            throw new RuntimeException('Memcached extension is not loaded');
        }

        // Create client
        $this->oClient = new Memcached();
        $this->oClient->setOption(Memcached::OPT_CONNECT_TIMEOUT, 10);
        $this->oClient->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        $this->oClient->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, count($aConfig['servers']));
        $this->oClient->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
        $this->oClient->setOption(Memcached::OPT_RETRY_TIMEOUT, 1);
        $this->oClient->addServers($aConfig['servers']);
    }

    public function get($sKey)
    {
        $sValue = $this->oClient->get($this->getKey($sKey));
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
