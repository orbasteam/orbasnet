<?php
/**
 * 載入樣板控制項
 * 
 * @author Ivan
 *
 */
class Orbas_Controller_Plugin_Template extends Zend_Controller_Plugin_Abstract
{
	public function getConfig()
	{
		$config = Zend_Controller_Action_HelperBroker::getStaticHelper('Config')->getConfig();
		
		if(isset($config->template)){
			return $config->template;
		}
		
		return null;
	}
	
	public function getTemplateConfig($module)
	{
		$config = $this->getConfig();
		
		if(isset($config->$module)){
			return $config->$module;
		}
		
		return null;
	}
	
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	$config = $this->getTemplateConfig($request->getModuleName());
    	if(!$config){
    		throw new Orbas_Application_Exception(
    			sprintf('template %s is not setting in Application.ini',
    				$request->getModuleName() 
    		));
    	}
    	
    	/*
    	 * 設定Layout Path 以及 view helper path
    	 */
    	$view = $this->getView();
    	$view->addHelperPath($config->view->helperPath, $config->view->helperPrefix);
    	$view->setScriptPath($config->view->scriptPath);

    	if($config->view->layoutPath){
    		Zend_Layout::startMvc($config->view->layoutPath);
    	} else {
    		$this->_initLayout();
    	}
    	
    	/*$controllerFront = Zend_Controller_Front::getInstance();
    	if(!$controllerFront->getParam('noErrorHandler')){
    		
    		# 設定error handler參數
    		$errorHandlerName = $this->getErrorHandler();
    		if($errorHandlerName){
    			$errorHandler = $controllerFront->getPlugin($errorHandlerName);
    			$errorHandler->setErrorHandler(array(
    				'module'		=> $request->getModuleName(),
    				'controller'	=> 'error',
    				'action'		=> 'error'
    			));
    		}
    	}*/
    }
    
    /**
     * 取得錯誤處理器
     * 
     * @return object
     */
    public function getErrorHandler()
    {
    	$config = Zend_Controller_Action_HelperBroker::getStaticHelper('Config')->getConfig();
    	$config = $config->toArray();
    	
    	if(isset($config['resources']['frontController']['plugins']['ErrorHandler'])){
    		return $config['resources']['frontController']['plugins']['ErrorHandler'];
    	}
    	
    	return null;
    }
    
    protected function _initLayout()
    {
    	$moduleName = $this->getRequest()->getModuleName();
    	$layoutPath = APPLICATION_PATH . '/views/scripts/' . $moduleName .'/';
    	
    	Zend_Layout::startMvc($layoutPath);
    }
    
    /**
     * 
     * @return Zend_View_Abstract
     */
    public function getView()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        return $viewRenderer->view;
    }
}
?>