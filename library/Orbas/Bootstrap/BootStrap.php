<?php
require_once APPLICATION_PATH . '/../data/enums/const.php';

class Orbas_Bootstrap_BootStrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * 初始化helper
	 * 
	 */
	protected function _initHelper()
	{
		Zend_Controller_Action_HelperBroker::addPath(
			APPLICATION_PATH . '/../library/Orbas', 'Orbas_'
		);
		
		Zend_Controller_Action_HelperBroker::addPath(
			APPLICATION_PATH . '/../library/Orbas/Helper', 'Orbas_Helper_'
		);
		
		# 建立session helper
		Zend_Controller_Action_HelperBroker::addHelper(new Orbas_Session());
	}

	/**
	 * Application.ini 設定檔加入Orbas_Config
	 * 
	 */
	protected function _initConfig()
	{
		$config = Zend_Controller_Action_HelperBroker::getStaticHelper('Config');
		$config->setConfig(new Zend_Config($this->getOptions(), true));
	}
	
	/**
	 * 設定目前語系
	 */
	protected function _initLocale()
    {
        $session = Zend_Controller_Action_HelperBroker::getStaticHelper('Session');
        
        $locale = $session->get(Orbas_Translate::SESSION_NAMESPACE);
        if($locale){
            Orbas_Translate::setLocale($locale);
        }
	}
	
	/**
	 * 設定翻譯器
	 * 
	 */
	protected function _initTranslator()
	{
	    $translate = new Orbas_Translate();
	    Zend_Controller_Action_HelperBroker::addHelper($translate);
	    
	    Zend_Registry::set('Zend_Translate', $translate->getTranslate());
	}
	
	/**
	 * 補足PHP版本不足的function
	 * 
	 */
	protected function _initFunctions()
	{
	    $path = APPLICATION_PATH . '/../library/Orbas/Functions/';
	    $directory = new DirectoryIterator($path);

	    foreach($directory as $file) {
	       
	       if ($file->isDot() || $file->isDir()) {
                continue;
            }
            
            $functionName = substr($file->getFilename(), 0, strrpos($file->getFilename(), '.'));
            if(!function_exists($functionName)) {
                require_once $path . $file->getFilename();
            }
	    }
	}
	
	protected function _initSessionExpireTime()
	{
	    Zend_Session::setOptions(array(
	    	'cookie_lifetime' => 0,
	    	'gc_maxlifetime'  => 3600
	    ));
	}
}
?>