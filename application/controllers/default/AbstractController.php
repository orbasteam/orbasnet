<?php
/**
 * Orbas 塗鴉牆抽象層
 * 
 * @author Ivan
 *
 */
abstract class AbstractController extends Orbas_Controller_Action
{
    /**
     * 404 page not found
     * 
     */
    protected function _pageNotFound()
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->forward('error', 'error');
    }
    
    public function preDispatch()
    {
        if(Orbas_Auth::getUser(ROLE_MEMBER)) {
            $this->_userInform();
            
            /*
             * 檢查是否有更新change-log
             */
            $changeLog = $this->getChangeLog();
            $lastChangeDate = new Zend_Date($changeLog->current()->date);
            $date = Zend_Date::now();
            if($date->compareDate($lastChangeDate) === 0) {
                $this->view->hasUpdate = true;
            }

            $this->getModel('User')->updateOnlineTime(Orbas_Auth::getUserInfo('SN', ROLE_MEMBER));
        }
    }
    
    /**
     * 使用者通知訊息
     * 
     */
    protected function _userInform()
    {
        $this->view->informQuantity = $this->_getUnreadQuantity();
    }
    
    /**
     * 未讀訊息數量
     * 
     * @var integer
     */
    protected $_unreadMessageCount = null;
    
    /**
     * 取得使用者未讀取訊息數量
     * 
     */
    protected function _getUnreadQuantity()
    {
        if($this->_unreadMessageCount === null) {
            
            $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
            $this->_unreadMessageCount = $this->getModel('Inform')->getUnreadQuantity($userSN);
        }
        
        return $this->_unreadMessageCount;
    }
    
    /**
     * 取得Orbas使用者
     * 
     * @return array|null
     */
    public function getUser()
    {
        $user = Orbas_Auth::getUser(ROLE_MEMBER);
        
        if($user) {
            return $user;
        }
        
        $cookie = $this->getRequest()->getCookie('auth_' . ROLE_MEMBER);
        if($cookie) {
            
            $cookie = unserialize(base64_decode($cookie));
            
            if(isset($cookie['key']) && isset($cookie['id'])) {
                $user = $this->getModel('UserSession')->fetchUser($cookie['key'], $cookie['id'], ROLE_MEMBER);

                $this->getHelper('Auth')->setUser($user, ROLE_MEMBER);
                
                if($user) return $user;
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @var Zend_Config
     */
    protected $_changeLog;
    
    /**
     * 
     * @throws Exception
     * @return Zend_Config
     */
    public function getChangeLog()
    {
        if(!$this->_changeLog) {
            $changeLogFile = APPLICATION_PATH . '/configs/changeLog/change.php';
            if(!file_exists($changeLogFile)) {
                throw new Exception('Change log file is not exists.');
            }
            
            $this->_changeLog = new Zend_Config(require $changeLogFile);
        }
        
        return $this->_changeLog;
    }
}
?>