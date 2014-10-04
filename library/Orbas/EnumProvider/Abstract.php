<?php
/**
 * 
 * 提供集合陣列抽象層
 * 
 * @author Ivan
 *
 */
abstract class Orbas_EnumProvider_Abstract
{
    /**
     * 
     * @var Zend_Controller_Action
     */
    protected $_controller;
    
    public function __construct(Zend_Controller_Action $controller)
    {
        $this->_controller = $controller;
    }
    
    /**
     * strategy pattern
     */
    public function direct()
    {
        
    }
}
?>