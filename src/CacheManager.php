<?php
namespace Asticode\CacheManager;

use Asticode\CacheManager\Handler\HandlerInterface;
use Asticode\Toolbox\ExtendedString;
use RuntimeException;

class CacheManager
{
    // Attributes
    private $sNamespace;
    private $aHandlers;
    private $aPriorityToNameMapping;

    // Construct
    public function __construct($sNamespace = 'Asticode\\CacheManager\\Handler')
    {
        // Initialize
        $this->sNamespace = $sNamespace;
        $this->aHandlers = [];
        $this->aPriorityToNameMapping = [];
    }

    /**
     * @param $sHandlerName
     * @param $sClassName
     * @param $iPriority
     * @param array $aConfig
     * @param string $sNamespace
     * @return CacheManager
     */
    public function addHandler($sHandlerName, $sClassName, $iPriority, array $aConfig, $sNamespace = '')
    {
        // Get class name
        $sClassName = ExtendedString::toCamelCase(sprintf(
            '%s\\%sHandler',
            $sNamespace === '' ? $this->sNamespace : $sNamespace,
            $sClassName
        ), '_', true);

        // Class name is valid
        if (!class_exists($sClassName)) {
            throw new RuntimeException(sprintf(
                'Invalid class name %s',
                $sClassName
            ));
        }

        // Priority is valid
        $iPriority = intval($iPriority);
        if (array_key_exists($iPriority, $this->aPriorityToNameMapping)) {
            throw new RuntimeException(sprintf(
                'Priority %s is already in use',
                $iPriority
            ));
        }

        // Add handler
        $oHandler = new $sClassName($aConfig);
        $this->aHandlers[$sHandlerName] = $oHandler;

        // Add priority
        $this->aPriorityToNameMapping[$iPriority] = $sHandlerName;
        ksort($this->aPriorityToNameMapping);

        // Return
        return $this;
    }

    /**
     * @param $sHandlerName
     * @return \Asticode\CacheManager\Handler\HandlerInterface
     */
    public function getHandler($sHandlerName)
    {
        if (!isset($this->aHandlers[$sHandlerName])) {
            throw new RuntimeException(sprintf(
                'Invalid handler name %s',
                $sHandlerName
            ));
        }

        return $this->aHandlers[$sHandlerName];
    }

    public function get($sKey)
    {
        // Loop through priorities
        foreach ($this->aPriorityToNameMapping as $sHandlerName) {
            $oData = $this->getHandler($sHandlerName)->get($sKey);
            if (!is_null($oData)) {
                return $oData;
            }
        }
        return null;
    }

    public function del($sKey)
    {
        // Loop through priorities
        foreach ($this->aPriorityToNameMapping as $sHandlerName) {
            $this->getHandler($sHandlerName)->del($sKey);
        }
    }

    public function test()
    {
        // Loop through handlers
        /** @var HandlerInterface $oHandler */
        foreach ($this->aHandlers as $oHandler) {
            $oHandler->test();
        }
    }
}

