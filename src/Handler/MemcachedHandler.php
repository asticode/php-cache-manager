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

        // Extend config
        $aConfig = ExtendedArray::extendWithDefaultValues(
            $aConfig,
            [
                'connection_timeout' => 10,
                'server_failure_limit' => 5,
                'remove_failed_servers' => true,
                'retry_timeout' => 1,
            ]
        );

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
        $this->oClient->setOption(Memcached::OPT_CONNECT_TIMEOUT, $aConfig['connection_timeout']);
        $this->oClient->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
        $this->oClient->setOption(Memcached::OPT_SERVER_FAILURE_LIMIT, $aConfig['server_failure_limit']);
        $this->oClient->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, $aConfig['remove_failed_servers']);
        $this->oClient->setOption(Memcached::OPT_RETRY_TIMEOUT, $aConfig['retry_timeout']);

        // Add servers
        $aServers = explode(',', $aConfig['servers']);
        foreach ($aServers as $sAddress) {
            // Valid address
            if (!empty($sAddress)) {
                // Initialize
                $aExplodedServer = explode(':', $sAddress);
                $sPort = array_pop($aExplodedServer);
                $sHost = implode(':', $aExplodedServer);

                // Add server
                $this->oClient->addServer($sHost, $sPort);
            }
        }
    }

    public static function createFromInstance(Memcached $oMemcached)
    {
        $oHandler = new MemcachedHandler();
        $oHandler->oClient = $oMemcached;
        return $oHandler;
    }

    public function get($sKey)
    {
        $sValue = $this->oClient->get($this->buildKey($sKey));
        return $sValue ? $this->unserialize($sValue) : null;
    }

    public function set($sKey, $oData, $iTTL = -1)
    {
        return $this->oClient->set($this->buildKey($sKey), $this->serialize($oData), $this->buildTTL($iTTL));
    }

    public function del($sKey)
    {
        $bSuccess = $this->oClient->delete($this->buildKey($sKey));
        return $this->oClient->getResultCode() !== Memcached::RES_NOTFOUND ? $bSuccess : true;
    }

    public function test()
    {
        // Initialize
        $sKey = 'key:test';
        $sValue = 'test';

        // Set value
        $this->set($sKey, $sValue, 0);

        // Get value
        $sCacheValue = $this->get($sKey);

        // Delete value
        $this->del($sKey);

        // Analyze cache value
        if ($sCacheValue !== $sValue) {
            throw new RuntimeException(sprintf(
                'Cache value <%s> is not the expected value <%s>',
                $sCacheValue,
                $sValue
            ));
        }
    }
}
