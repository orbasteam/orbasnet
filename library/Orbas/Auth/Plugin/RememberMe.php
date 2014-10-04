<?php
/**
 * Remember Me plugin
 * 
 * @author Ivan
 *
 */
class Orbas_Auth_Plugin_RememberMe implements Orbas_Auth_Plugin_Interface
{
    
    /**
	 * 儲存使用者sessin id的資料表
	 * 
	 * @var string
	 */
	protected $_sessionTable = 'user_session';
    
	/* (non-PHPdoc)
	 * @see Orbas_Auth_Plugin_Interface::loginFailure()
	 */
	public function loginFailure (Orbas_Auth $auth, 
			Orbas_Auth_Exception $exception, $data, $role)
	{
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see Orbas_Auth_Plugin_Interface::loginSuccess()
	 */
	public function loginSuccess (Orbas_Auth $auth, $data, $role)
	{
		$id = md5(uniqid($data['SN']));
	    $db = Zend_Db_Table::getDefaultAdapter();
	    $db->insert($this->_sessionTable, array(
            'SESSION_ID' => $id,
            'USER_SN'    => $data['SN'],
	        'ROLE'       => $role,
	        'DATETIME'   => date('Y-m-d H:i:s')
	    ));
	    
	    $value = array(
            'key' => $db->lastInsertId($this->_sessionTable),
	        'id'  => $id
	    );
	    
	    $cookie = new Zend_Http_Header_SetCookie();
        $cookie->setName('auth_' . $role)
               ->setPath('/')
               ->setValue(base64_encode(serialize($value)))
               ->setExpires(time() + (10 * 365 * 24 * 60 * 60));
        
        $response = Zend_Controller_Front::getInstance()->getResponse();
        $response->setRawHeader($cookie);
	}

	/* (non-PHPdoc)
	 * @see Orbas_Auth_Plugin_Interface::preLogin()
	 */
	public function preLogin (Orbas_Auth $auth, $data, $role)
	{
		
	}

	/* (non-PHPdoc)
	 * @see Orbas_Auth_Plugin_Interface::preLogout()
	 */
	public function preLogout ($role)
	{
		$cookie = new Zend_Http_Header_SetCookie();
		$cookie->setName('auth_' . $role)
		       ->setPath('/')
		       ->setValue('')
		       ->setExpires(time() - 3600);
		
		$response = Zend_Controller_Front::getInstance()->getResponse();
        $response->setRawHeader($cookie);
	}
}
?>