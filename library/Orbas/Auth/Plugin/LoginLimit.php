<?php
/**
 * 
 * 登入次數限制
 * 
 * @author Ivan
 *
 */
class Orbas_Auth_Plugin_LoginLimit implements Orbas_Auth_Plugin_Interface
{
    const SESSION_LOGIN_LIMIT = '__sessionLoginLimit';
    
    /**
     * 次數限制
     * 
     * @var integer
     */
    protected $_limit = 3;
    
    /**
	 * @return the $_limit
	 */
	public function getLimit ()
	{
		return $this->_limit;
	}

	/**
	 * @param number $_limit
	 */
	public function setLimit ($_limit)
	{
		$this->_limit = $_limit;
		return $this;
	}

	/**
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        foreach($options as $key => $option) {
            $methodName = 'set' . ucfirst($key);
            if(method_exists($this, $methodName)) {
                $this->$methodName($option);
            }
        }
    }
    
    /**
     * 
     * @return Orbas_Session
     */
    public function getSession()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('Session');
    }
    
    public function preLogout ($role)
    {
    	
    }
    
    public function preLogin (Orbas_Auth $auth, $data, $role)
    {
    	$times = $this->getSession()->get(self::SESSION_LOGIN_LIMIT . $role);
    	if($times && $times > $this->getLimit()) {
    	    throw new Orbas_Auth_Exception('failureCredentialInvalid', Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
    	}
    }
    
    /* (non-PHPdoc)
	 * @see Orbas_Auth_Plugin_Interface::loginSuccess()
	 */
	public function loginSuccess (Orbas_Auth $auth, $data, $role)
	{
	    $name = self::SESSION_LOGIN_LIMIT . $role;
	    unset($this->getSession()->$name);
	}

	public function loginFailure (Orbas_Auth $auth, Orbas_Auth_Exception $exception, $data, $role)
    {
        $times = $this->getSession()->get(self::SESSION_LOGIN_LIMIT . $role);
        if(!$times) $times = 0;
        
        $this->getSession()->set(self::SESSION_LOGIN_LIMIT . $role, ++$times);
    }
}
?>