<?php
class Orbas_Config extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * Application.ini 設定值
	 * 
	 * @var Zend_Config
	 */
	protected $_config;
	
	public function setConfig(Zend_Config $config)
	{
		$this->_config = $config;
	}
	
	public function getConfig()
	{
		return $this->_config;
	}
	
	public function __get($name)
	{
	    if(isset($this->_config->$name)){
	        return $this->_config->$name;
	    }
	    
	    return null;
	}
}
?>