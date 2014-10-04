<?php
require_once 'library/GoogleAPI/Google_Client.php';
require_once 'library/GoogleAPI/contrib/Google_Oauth2Service.php';

require_once 'AbstractController.php';

/**
 * 處理帳務相關動作
 * 
 * 登入、登出、註冊、驗證email...等
 * 
 * @author Ivan
 *
 */
class AccountController extends AbstractController
{
	protected $_modelName = 'User';
	
	public function indexAction()
	{
		
	}
	
	public function loginAction()
	{
	    if($this->getUser()) {
	        $this->_helper->redirector('index', 'index');
	    }
	    
	    $this->_helper->layout()->disableLayout();
	    $this->getMessageFromSession();
	}
	
	public function loginDoAction()
	{
		try {
		    
		    if(!$this->getRequest()->isPost()) {
		        $this->_pageNotFound();
		    }
		    
		    $postData = $this->getRequest()->getPost();
		    
		    $auth = $this->getHelper('Auth');
		    $auth->setIdentityColumn('EMAIL');
		    $auth->setEnableCaptcha(false);
		    
		    if(!empty($postData['remember'])) {
		        $plugin = new Orbas_Auth_Plugin_RememberMe();
		        $auth->addPlugin($plugin);
		    }
		    
			$auth->login($postData, ROLE_MEMBER);
			
			$this->_helper->redirector('index', 'index');
		    
		} catch (Exception $e) {
            if($e->getCode() == Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID) {
                $this->setMessageIntoSession($this->_('failureCredentialInvalid'));
		    } else {
		        $this->setMessageIntoSession($this->_($e->getMessage()));
            }
		}
		
		$this->view->data = $postData;
		$this->forward('login');
	}
	
	public function logoutAction()
	{
	    $auth = $this->getHelper('Auth');
	    $auth->addPlugin(new Orbas_Auth_Plugin_RememberMe());
		$auth->logout(ROLE_MEMBER);
		
		$this->_helper->redirector('login');
	}
	
	/**
	 * 註冊頁面
	 * 
	 */
	public function signUpAction()
	{
		
	}
}
?>