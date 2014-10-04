<?php
abstract class Orbas_Controller_Action extends Zend_Controller_Action
{
	/**
	 * default Model
	 * 
	 * @var string
	 */
	protected $_modelName;
	
	/**
	 * 取得application.ini 設定檔
	 *
	 * @return Orbas_Config
	 */
	public function getApplicationConfig()
	{
		return $this->getHelper('Config');
	}
	
	/**
	 * 取得model
	 * 
	 * @param string $name
	 * @return Orbas_Model_Abstract
	 */
	public function getModel($name = null)
	{
		if(null === $name){
			$name = $this->getModelName();
		}
		
		if(strpos($name, '_') === false){
			$moduleName = $this->getRequest()->getModuleName();
			$name = $moduleName . '_' . $name;
		}
		
		return Orbas_Model_Broker::get($name);
	}
	
	/**
	 * 取得預設model名稱
	 * 
	 */
	public function getModelName()
	{
		if($this->_modelName){
			return $this->_modelName;
		}
		
		return $this->getName();
	}
	
	public function setModelName($name)
	{
		$this->_modelName = $name;
		return $this;
	}

	/**
	 * 取得Module_Controller名稱
	 * 
	 * @return string
	 */
	public function getName()
	{
	    $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
	    $request    = $this->getRequest();
	    
	    $controllerClass = $dispatcher->getControllerClass($request);
	    $controllerClassName = $dispatcher->formatClassName($request->getModuleName(), $controllerClass);
	    
	    $controllerPosition = strpos($controllerClassName, 'Controller');
	    
	    return substr($controllerClassName, 0, -10);
	}
	
	/**
	 * 設定session
	 * 
	 * @param string $name
	 * @param mixed  $value
	 */
	public function sessionSet($name, $value)
	{
		$this->getHelper('Session')->set($name, $value);
		return $this;
	}
	
	/**
	 * 取得session值
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function sessionGet($name)
	{
		return $this->getHelper('Session')->get($name);
	}
	
	/**
	 * session 是否設置
	 * 
	 * @param string $name
	 */
	public function sessionIsset($name)
	{
		$session = $this->getHelper('Session');
		return isset($session->$name);
	}
	
	/**
	 * unset session
	 * 
	 * @param string $name
	 */
	public function sessionUnset($name)
	{
		$session = $this->getHelper('Session');
		unset($session->$name);
	}
	
	const MESSAGE_INFO     = 'info';
	const MESSAGE_WARNING  = 'warning';
	const MESSAGE_ERROR    = 'error';
	const MESSAGE_SUCCESS  = 'success';
	
	/**
	 * 設定message至session
	 * 
	 * @param string $message
	 * @param string $type
	 */
	public function setMessageIntoSession($message, $type = self::MESSAGE_ERROR)
	{
	    $this->sessionSet('message', array(
            'type' => $type,
            'message' => $message
	    ));
	}
	
	/**
	 * 從session取得message
	 * 
	 */
	public function getMessageFromSession()
	{
		if($this->sessionIsset('message')){
		    
		    $message = $this->sessionGet('message');
		    if(is_array($message)) {
		        $this->_setViewMessage($message['message'], $message['type']);
		    } else {
		        $this->_setViewMessage($message);
		    }
			
			$this->sessionUnset('message');
		}
	}
	
	/**
	 * 處理錯誤訊息
	 * 
	 * @param Exception $e
	 */
	protected function _handelErrorMessage(Exception $e)
	{
		$this->_setViewMessage($e->getMessage(), self::MESSAGE_ERROR, true);
	}
	
	/**
	 * 提供view訊息
	 * 
	 * @param string $message  訊息
	 * @param string $type     訊息種類
	 * @param boolean $translate 是否翻譯
	 */
	protected function _setViewMessage($message, $type = self::MESSAGE_ERROR, $translate = false)
	{
		if($translate){
			$message = $this->_($message);
		}
		
		$this->view->message = array(
			'type' => $type,
		    'message' => $message
		);
	}
	
	/**
	 * translate
	 * 
	 * @param string $messageId
	 */
	protected function _($messageId)
	{
		return $this->getHelper('Translate')->_($messageId);
	}
	
	/**
	 * 使用ajax成功時回傳的json
	 * 
	 */
	protected function _ajaxSuccess()
	{
	    $this->_helper->json(array('error' => 0));
	}
	
	/**
	 * 使用ajax錯誤時回傳的json
	 * 
	 * @param string $message
	 */
	protected function _ajaxError($message)
	{
	    $this->_helper->json(array(
	    	'error'   => 1,
            'message' => $message
	    ));
	}
	
    /**
	 * Zend Debug Dump
	 * @param mixed $var
	 * @param string $label
	 */
	static public function dump($var)
	{
	    Zend_Debug::dump($var);
	    exit;
	} 
}
?>