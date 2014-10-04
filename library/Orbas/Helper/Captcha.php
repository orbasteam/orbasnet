<?php
/**
 * 取得驗證碼
 *
 */
class Orbas_Helper_Captcha extends Zend_Controller_Action_Helper_Abstract 
{
	const SESSION_CAPTCHA = '__session_captcha';
	
	/**
	 * 產生captcha的物件
	 * 
	 * @var Zend_Captcha_Base
	 */
	static protected $_captcha;
	
	/**
	 * 產生captcha圖片並回傳檔名
	 * 
	 */
	public function get()
	{
		$this->getCaptcha()
			 ->setTimeout(300)
			 ->setWordlen(4)
			 ->setWidth(80)
			 ->setHeight(25)
			 ->setFontSize(16)
			 ->setFont(APPLICATION_PATH . '/../data/resources/fonts/ARIAL.TTF')
			 ->setImgDir($this->getCaptchaPath());
			 
		return $this->getCaptcha()->generate();
	}
	
	/**
	 * 直接輸出至螢幕上
	 * 
	 */
	public function outPut()
	{
		$fileName = $this->get();
		switch ($this->getCaptcha()->getSuffix()){
			
			case '.gif':
				$mimeType = 'image/gif';
				break;
			
			case '.jpg':
				$mimeType = 'image/jpeg';
				break;
				
			case '.png':
				$mimeType = 'image/png';
				break;
				
			default;
				throw new Orbas_Application_Exception('Wrong captcha suffix');
				break;
		}
		
		$session = Zend_Controller_Action_HelperBroker::getStaticHelper('Session');
		$session->set(self::SESSION_CAPTCHA, $this->getCaptcha()->getWord());
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer');
		$viewRenderer->setNeverRender();
		
		$this->getResponse()
			 ->setHeader("Content-type", $mimeType)
			 ->sendHeaders();

		readfile($this->getCaptchaPath() . '/' . $fileName . $this->getCaptcha()->getSuffix());
		
		$this->getResponse()->sendResponse();
		
	}
	
	public function getCaptcha()
	{
		if(!self::$_captcha){
			self::$_captcha = new Orbas_Captcha_Image();
		}
		
		return self::$_captcha;
	}
	
	/**
	 * 取得captcha 文字
	 * 
	 */
	static public function getWord()
	{
		$session = Zend_Controller_Action_HelperBroker::getStaticHelper('Session');
		return $session->get(self::SESSION_CAPTCHA);
	}
	
	public function getCaptchaPath()
	{
		$config = Zend_Controller_Action_HelperBroker::getStaticHelper('Config');
		
		if(isset($config->captcha)){
			return $config->captcha->path;
		}
		
		return APPLICATION_PATH . '/../data/captcha';
	}
}

?>