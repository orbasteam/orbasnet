<?php
/**
 * Session 運用
 * 
 * @author Ivan
 *
 */
class Orbas_Session extends Zend_Controller_Action_Helper_Abstract
{
	const ORBAS_NAMESPACE = 'orbasNamespace';
	
	/**
	 * 
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	
	/**
	 * 取得session
	 * 
	 * @param string $name
	 */
	public function get($name)
	{
		$this->_setup();
		
		if(isset($this->_session->$name)) {
			return $this->_session->$name;
		}
		
		return null;
	}
	
	public function __get($name)
	{
		return $this->get($name);
	}
	
	/**
	 * 設定session
	 * 
	 * @param string $name
	 * @param string|integer $value
	 */
	public function set($name, $value)
	{
		$this->_setup();
		$this->_session->$name = $value;
		return $this;
	}
	
	public function __isset($name)
	{
		$this->_setup();

		return isset($this->_session->$name);
	}
	
	public function __unset($name)
	{
		$this->_setup();
		
		if(isset($this->_session->$name)){
			unset($this->_session->$name);
		}
		
		return $this;
	}
	
	protected function _setup()
	{
		if(!$this->_session) {
			$this->_session = new Zend_Session_Namespace(self::ORBAS_NAMESPACE);
		}
	}
}
?>