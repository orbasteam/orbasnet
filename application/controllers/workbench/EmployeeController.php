<?php
/**
 * 後臺使用者
 * 
 * @author Ivan
 *
 */
class Workbench_EmployeeController extends Orbas_Controller_Workbench
{
	/**
	 * 登入
	 * 
	 */
	public function loginAction()
	{
		$this->_helper->layout()->disableLayout();
	}
	
	/**
	 * 執行登入動作
	 */
	public function loginDoAction()
	{
	    $postData = $this->getRequest()->getPost();
	    
		try{
		    $auth = $this->getHelper('Auth');
		    $auth->setIdentityColumn('UID');
			$auth->login($postData, 'administrator');
			
			$this->_helper->redirector('index');
			
		} catch (Exception $e) {
		    $this->_handelErrorMessage($e);
		}
		
		$this->view->data = $postData;
		$this->forward('login');
	}
	
	/**
	 * 登出
	 */
	public function logoutAction()
	{
		$this->getHelper('Auth')->logout('administrator');
		
		$this->_helper->redirector('login');
	}
	
	public function xmlAction()
	{
		
	}
	
	/* (non-PHPdoc)
	 * @see Orbas_Controller_Workbench::_getEditBreadcrumb()
	 */
	protected function _getEditBreadcrumb(Orbas_DataObject $row) {
		// TODO Auto-generated method stub
		
	}
}
?>