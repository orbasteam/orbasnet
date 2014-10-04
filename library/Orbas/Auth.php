<?php
/** 
 * 登入驗證
 * 
 * @author Ivan
 * 
 */
class Orbas_Auth extends Zend_Controller_Action_Helper_Abstract
{
    # 後台管理員
    const ROLE_ADMIN = 'administrator';

    /**
     * 
     * @var Zend_Acl
     */
    static protected $_acl;
    
	/**
     * 
     * @return Zend_Acl
     */
    static public function getAcl()
    {
        if(!self::$_acl) {
            self::$_acl = new Zend_Acl();
        }
        
        return self::$_acl;
    }
    
    /**
     * 
     * @param Zend_Acl $acl
     */
    static public function setAcl(Zend_Acl $acl)
    {
        self::$_acl = $acl;
    }
    
	/**
	 * 圖形驗證碼的欄位名稱
	 * 
	 * @var string
	 */
	protected $_captchaColumn = 'captcha';
	
	/**
     * @return the $_captchaColumn
     */
    public function getCaptchaColumn ()
    {
        return $this->_captchaColumn;
    }

	/**
     * @param field_type $_captchaColumn
     */
    public function setCaptchaColumn ($_captchaColumn)
    {
        $this->_captchaColumn = $_captchaColumn;
        return $this;
    }
	
	/**
	 * 帳號欄位名稱
	 * 
	 * @var string
	 */
	protected $_identityColumn = 'ID';
	
	/**
     * @return the $_identityColumn
     */
    public function getIdentityColumn ()
    {
        return $this->_identityColumn;
    }
    
	/**
     * @param string $_identityColumn
     */
    public function setIdentityColumn ($_identityColumn)
    {
        $this->_identityColumn = $_identityColumn;
        return $this;
    }
	
	/**
	 * 密碼欄位名稱
	 * 
	 * @var string
	 */
	protected $_credentialColumn = 'PASSWORD';
	
	/**
     * @return the $_credentialColumn
     */
    public function getCredentialColumn ()
    {
        return $this->_credentialColumn;
    }
    
    /**
     * 是否啟用圖形驗證
     * 
     * @var bool
     */
    protected $_enableCaptcha = true;

	/**
     * @return the $_enableCaptcha
     */
    public function getEnableCaptcha ()
    {
        return $this->_enableCaptcha;
    }

	/**
     * @param bool $_enableCaptcha
     */
    public function setEnableCaptcha ($_enableCaptcha = true)
    {
        $this->_enableCaptcha = $_enableCaptcha;
        return $this;
    }

	/**
     * @param string $_credentialColumn
     */
    public function setCredentialColumn ($_credentialColumn)
    {
        $this->_credentialColumn = $_credentialColumn;
        return $this;
    }
    
    /**
     * 登入驗證的資料表
     * 
     * @var string
     */
    protected $_tableName;
	
	/**
     * @return the $_tableName
     */
    public function getTableName ()
    {
    	if(!$this->_tableName){
    		$this->_tableName = $this->getActionController()
    								 ->getModel()
    								 ->info(Zend_Db_Table::NAME);
    	}
    	
        return $this->_tableName;
    }

	/**
     * @param field_type $_tableName
     */
    public function setTableName ($_tableName)
    {
        $this->_tableName = $_tableName;
        return $this;
    }
    
	/**
	 * 
	 * @var Zend_Auth_Adapter_DbTable
	 */
	protected $_authAdapter;

	/**
	 *
	 * @return Zend_Auth_Adapter_DbTable
	 */
	public function getAuthAdapter()
	{
		if(!$this->_authAdapter){
			$this->_authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
			$this->_authAdapter
				 ->setTableName($this->getTableName())
				 ->setIdentityColumn($this->getIdentityColumn())
				 ->setCredentialColumn($this->getCredentialColumn());
		}
		
		return $this->_authAdapter;
	}
	
	/**
	 * 登入
	 * 
	 * @param array  $data 登入資料
	 * @param string $role 登入身分
	 * @param boolean $crypt 密碼是否加密
	 */
	public function login($data, $role, $crypt = false)
	{
		/*
		 * 啟用驗證碼
		 */
		if($this->getEnableCaptcha()){
		    $word = Orbas_Helper_Captcha::getWord();
			if(!isset($data[$this->getCaptchaColumn()]) || $word != $data[$this->getCaptchaColumn()]) {
				throw new Orbas_Auth_Exception('驗證碼錯誤');
			}
		}
		
		$data = new Orbas_DataObject($data);
		
		$this->_notifyPlugin('preLogin', $this, $data, $role);
		
		$identity	= $data[$this->getIdentityColumn()];
		$credential	= $data[$this->getCredentialColumn()];

		/*
		 * 密碼加密
		 */
		if($crypt) {
		    $credential = $this->getCryptHelper()->crypt($credential, $identity);
		}
		
		$this->_authenticate($identity, $credential, $role);
	}
	
	/**
	 * 
	 * @var array
	 */
	protected $_plugins = array();
	
	public function addPlugin(Orbas_Auth_Plugin_Interface $plugin) 
	{
        $this->_plugins[] = $plugin;
        return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Orbas_Auth_Plugin_Interface|null
	 */
	public function getPlugin($name)
	{
	    foreach($this->_plugins as $plugin) {
	        
	        $class = get_class($plugin);
	        $className = substr($class, strrpos($class, '_')+1);
	        
	        if(strtolower($className) == strtolower($name)) {
	            return $plugin;
	        }
	    }
	    
	    return null;
	}
	
	/**
	 * 通知外掛物件
	 * 
	 * @param string $methodName
	 * @param array  $args
	 */
	protected function _notifyPlugin($methodName)
	{
	    foreach($this->_plugins as $plugin) {
	        if(!method_exists($plugin, $methodName)) {
	            throw new Orbas_Application_Exception('method "' . $methodName . '" is not callable.');
	        }
	        
	        $args = array_slice(func_get_args(), 1);
	        call_user_func_array(array($plugin, $methodName), $args);
	    }
	}
	
	/**
	 * 密碼加密工具
	 * 
	 * @return Orbas_Helper_Crypt
	 */
	public function getCryptHelper()
	{
	   return Zend_Controller_Action_HelperBroker::getStaticHelper('crypt'); 
	}
	
	/**
	 * 取得登入者
	 * 
	 * @param string $role
	 */
	public function getIdentity($role = self::ROLE_ADMIN)
	{
		return self::getUser($role);
	}
	
	/**
	 * 登入者
	 * 
	 * @var array
	 */
	static protected $_storage = array();
	
	/**
	 * 
	 * @param string $role
	 * @return Zend_Auth_Storage_Interface
	 */
	static public function getStorage($role)
	{
	    if(!isset(self::$_storage[$role])) {
	        self::$_storage[$role] = new Orbas_Auth_Storage_Session($role);
	    }
	    
	    return self::$_storage[$role];
	}
	
	/**
	 * Set Storage
	 * 
	 * @param string $role
	 * @param Zend_Auth_Storage_Interface $storage
	 */
	static public function setStorage($role, Zend_Auth_Storage_Interface $storage)
	{
	    self::$_storage[$role] = $storage;
	}
	
	/**
	 * 靜態方式取得登入者
	 * 
	 * @param string $role
	 */
	static public function getUser($role = self::ROLE_ADMIN)
	{
	    $user = self::getStorage($role);
	    return $user->read();
	}
	
    /**
     * 取得登入的使用者單一資料
     * 
     * @param string $data
     * @param string $role
     */
	static public function getUserInfo($data, $role = self::ROLE_ADMIN)
	{
	    $user = self::getUser($role);
	    
	    if(isset($user[$data])) {
	        return $user[$data];
	    }
	    
	    return false;
	}
	
	/**
	 * 設定使用者
	 * 
	 * 直接從外部登入，不須經過驗證
	 *
	 * @param $data
	 */
	public function setUser($data, $role)
	{
	    $storage = self::getStorage($role);
	    $storage->write($data);
	    
		return $this;
	}
	
	/**
	 * 驗證
	 * 
	 * @param string $identity
	 * @param string $credential
	 * 
	 */
	protected function _authenticate($identity, $credential, $role)
	{
	    $adapter = $this->getAuthAdapter();
		$result  = $adapter->setIdentity($identity)
                		   ->setCredential($credential)
    		               ->authenticate();
		
        $rowObject = $adapter->getResultRowObject();
        
        if(!$result->isValid()){
            $exception = new Orbas_Auth_Exception($result->getMessages(), $result->getCode());
            $data = array(
            	'identity' => $identity,
                'credential' => $credential
            );
		    $this->_notifyPlugin('loginFailure', $this, $exception, $data, $role);
		    throw $exception;
        }
		
		$storage = self::getStorage($role);
		$storage->write($rowObject);
		
		$this->_notifyPlugin('loginSuccess', $this, get_object_vars($rowObject), $role);
	}
	
	/**
	 * 登出
	 * 
	 * @param string $role  登出身分
	 */
	public function logout($role = self::ROLE_ADMIN)
	{
	    $this->_notifyPlugin('preLogout', $role);
	    
	    $storage = self::getStorage($role);
	    $storage->clear();
	}
}
?>