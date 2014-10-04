<?php
/**
 * 檢查頁面是否有權限進入
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * 權限設定檔
     * 路徑在application/configs/acl/config.php
     * 
     * @var array
     */
    protected $_config;
    
    /**
     * 取得權限設定檔
     * 
     * @throws Orbas_Application_Exception
     * @return array
     */
    public function getConfig()
    {
        if(!$this->_config){
            
            $configPath = APPLICATION_PATH . '/configs/acl/config.php';
            if(!file_exists($configPath)){
                throw new Orbas_Application_Exception('Acl file is not exists');
            }
            
            $this->_config = require $configPath;
        }
        
        return $this->_config;
    }
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $request->getModuleName();
        $config     = Zend_Controller_Action_HelperBroker::getStaticHelper('Config');

        # 檢查該模組是否需要登入或權限
        if(isset($config->acl->$moduleName)){
        
            # 取得是否有該模組需要的角色登入者
            $auth  = Zend_Controller_Action_HelperBroker::getStaticHelper('Auth');
            $roles = Orbas_Auth::getAcl()->getRoles();
            $identity = null;
            if($roles){
                foreach($roles as $role){
                    $identity = $auth->getIdentity($role);
                    
                    if($identity){
                        break;
                    }
                }
            }
            
            # 如果該模組角色沒登入，則導至登入頁
            if(!$identity){
                
                $camelCaseFilter = new Zend_Filter_Word_SeparatorToCamelCase('.');
                
                # 檢查該頁是否為開放頁
                $controllerName = $camelCaseFilter->filter($request->getControllerName());
                $actionName     = $request->getActionName();
                
                # 開放頁
                $publicAction = $config->acl->$moduleName->public->$controllerName;
                
                /*
                 * 沒有在開放action中，則redirect至登入頁
                 */
                if($publicAction != '*'){
                	
                	$publicAction = explode(',', $publicAction);
                	
                	if(!in_array($actionName, $publicAction)){
                		$loginPage = $config->acl->$moduleName->login;
	                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
	                    $redirector->gotoUrl($loginPage);
                	}
                }
            }
            
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->view->user = $identity;
        }
    }
    
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        # 設定權限資料
        $this->_setupAcl();
    }
    
    /**
     * 從acl config中設定權限資料
     */
    protected function _setupAcl()
    {
        $acl = Orbas_Auth::getAcl();
        
        foreach($this->getConfig() as $roleId => $data){
        
        	$parent = null;
        
        	# 該角色是否有繼承的角色
        	if(isset($data['parent'])){
        	    $parent = $data['parent'];
        	}
        
    		$acl->addRole($roleId, $parent);
    
    		# 增加該角色的資源及權限
    		if(isset($data['resources']) || isset($data['privileges'])){
    
        		$resources  = null;
        		$privileges = null;
    
    		    if(!empty($data['resources'])){
    		    	$resources = $data['resources'];
    			    foreach($resources as $resourceId){
    			        
    			        if(!$acl->has($resourceId)){
    		                $acl->addResource($resourceId);
    			        }
    		        }
                }
    
    			if(!empty($data['privileges'])){
    			    $privileges = $data['privileges'];
                }
    
                $acl->allow($roleId, $resources, $privileges);
    		}
    	}
    }
}    
?>