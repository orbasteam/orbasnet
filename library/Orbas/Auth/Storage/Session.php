<?php
/**
 * 存放驗證後的使用者資料
 *  
 * @author Ivan
 * 
 */
class Orbas_Auth_Storage_Session implements Zend_Auth_Storage_Interface
{
	const Orbas_Auth_Session_Namespace = 'orbas_auth_session_namespace';
	
	/**
	 * 登入角色
	 * 
	 * @var string
	 */
	protected $_role;
	
	/**
	 * @return the $_role
	 */
	public function getRole() 
	{
		return $this->_role;
	}

	/**
	 * @param string $_role
	 */
	public function setRole($_role) 
	{
		$this->_role = $_role;
		return $this;
	}
	
	/**
	 * 
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	
	/**
	 * 
	 * @param string  $role 登入角色
	 * @param boolean $rememberMe
	 */
	public function __construct($role)
	{
		$this->_role = $role;
		$this->_session = new Zend_Session_Namespace(self::Orbas_Auth_Session_Namespace);
	}
	
	/* (non-PHPdoc)
     * @see Zend_Auth_Storage_Interface::clear()
     */
    public function clear ()
    {
    	unset($this->_session->{$this->_role});
    	Zend_Session::forgetMe();
    }

	/* (non-PHPdoc)
     * @see Zend_Auth_Storage_Interface::isEmpty()
     */
    public function isEmpty ()
    {
        return !isset($this->_session->{$this->_role});
    }

	/* (non-PHPdoc)
     * @see Zend_Auth_Storage_Interface::read()
     */
    public function read ()
    {
    	return $this->_session->{$this->_role};
    }

	/* (non-PHPdoc)
     * @see Zend_Auth_Storage_Interface::write()
     */
    public function write ($contents)
    {
        if(is_object($contents)) {
            $contents = get_object_vars($contents);
        }
        
    	$this->_session->{$this->_role} = $contents;
    }
}
?>