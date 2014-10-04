<?php
/**
 * 取得Zend_Mail物件
 * 
 * @author Ivan
 *
 */
class Orbas_Mail extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * 
	 * @var Zend_Mail
	 */
	static protected $_mail;
	
	/**
	 * Mail 物件初始化
	 * 
	 */
	static protected function _initMail()
	{
		$config = Zend_Controller_Action_HelperBroker::getStaticHelper('Config');
		
		if(isset($config->mail)) {
		    $mailConfig = $config->mail;
		
    		# 設定SMTP資訊
    		$transport = new Zend_Mail_Transport_Smtp(
    			$mailConfig->smtp->host, $mailConfig->smtp->params->toArray());
    		
    		Zend_Mail::setDefaultTransport($transport);
    		Zend_Mail::setDefaultFrom($mailConfig->from->addr, $mailConfig->from->name);
		} else {
		    
		    $transport = new Zend_Mail_Transport_Sendmail();
		    Zend_Mail::setDefaultTransport($transport);
		    Zend_Mail::setDefaultFrom('service@orbas.net');
		}
		
		self::$_mail = new Zend_Mail('UTF-8');
	}

	static public function zendMail()
	{
		if(!self::$_mail){
			self::_initMail();
		}
		
		return self::$_mail;
	}
}
?>