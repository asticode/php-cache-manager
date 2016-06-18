<?php
namespace Asticode\CacheManager\Handler;

use Asticode\Toolbox\ExtendedArray;
use Redis;
use RuntimeException;

class RedisHandler extends AbstractHandler implements HandlerInterface
{
    // Attributes
    /** @var \Redis $oClient */
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
                'timeout' => 0.0,
            ]
        );

        // Check Redis is loaded
        if (!extension_loaded('redis')) {
            throw new RuntimeException('Redis extension is not loaded');
        }

        // Create client
        $this->oClient = new Redis();

        // Add servers
        if (array_key_exists('servers', $aConfig)) {
            $aServers = array_filter(explode(',', $aConfig['servers']));
            foreach ($aServers as $sAddress) {
                // Parse address
                list($sHost, $sPort) = array_pad(explode(':', $sAddress), 2, '');

                // Connect
                $this->oClient->connect($sHost, intval($sPort), $aConfig['timeout']);
            }
        }
    }

    public static function createFromInstance(Redis $oRedis)
    {
        $oHandler = new RedisHandler();
        $oHandler->oClient = $oRedis;
        return $oHandler;
    }

    public function get($sKey)
    {
        $sValue = $this->oClient->get($this->buildKey($sKey));
        return $sValue ? $this->unserialize($sValue) : null;
    }

    public function set($sKey, $oData, $iTTL = -1)
    {
        $iTTL = $this->buildTTL($iTTL);
        if ($iTTL === 0) {
            return $this->oClient->set($this->buildKey($sKey), $this->serialize($oData));
        }
        return $this->oClient->setex($this->buildKey($sKey), $iTTL, $this->serialize($oData));
    }

    public function del($sKey)
    {
        $iNumberOfKeysDeleted = $this->oClient->del($this->buildKey($sKey));
        return $iNumberOfKeysDeleted === 1 ? true : false;
    }

    public function test()
    {
        // Initialize
        $sKey = 'key:test';
        $sValue = 'test';

        // Set value
        $bSuccess = $this->set($sKey, $sValue, 0);
        if (!$bSuccess) {
            throw new RuntimeException(sprintf(
                'Unsuccessful set for value <%s>',
                $sValue
            ));
        }

        // Get value
        $sCacheValue = $this->get($sKey);

        // Delete value
        //$this->del($sKey);

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
