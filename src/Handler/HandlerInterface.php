<?php
namespace Asticode\CacheManager\Handler;

interface HandlerInterface
{

    /**
     * @param string $sKey
     * @return mixed Returns NULL on failure, and the stored value on success
     */
    public function get($sKey);

    /**
     * @param string $sKey
     * @param $oData
     * @param int $iTTL
     * @return bool Returns TRUE on success and FALSE on failure
     */
    public function set($sKey, $oData, $iTTL = -1);

    /**
     * @param string $sKey
     * @return bool Returns TRUE on success and FALSE on failure
     */
    public function del($sKey);
}
