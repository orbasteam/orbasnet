<?php
/**
 * 驗證登入時的例外
 *  
 * @author Ivan
 * 
 */
class Orbas_Auth_Exception extends Exception
{
    /**
     * 由Zend_Auth_Result取得的訊息為array
     * 
     * $_messages 放置原有的錯誤訊息
     * $message 則放置第一筆錯誤訊息
     * 
     * @var array
     */
    protected $_messages;
    
	public function __construct ($messages, $code = null, $previous = null) 
	{
	    $messages = (array)$messages;
	    
	    $this->_messages = $messages;
		$this->message   = array_pop($messages);
		$this->code      = $code;
	}
	
	public function getMessages()
	{
	    return $this->message;
	}
}
?>