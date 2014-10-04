<?php
require_once 'AbstractController.php';

/**
 * 訊息通知
 * 
 * @author Ivan
 *
 */
class InformController extends AbstractController
{
    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $user = Orbas_Auth::getUser(ROLE_MEMBER);
        
        # 一次取得5筆訊息
        $rows = $this->getModel('Inform')->fetchForView($user['SN'], 5);
        $this->view->informs = $rows;
        
        $this->_setInformMessageRead();
    }
    
    /**
     * 設定全部訊息為已讀
     * 
     */
    protected function _setInformMessageRead()
    {
        $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
        $this->getModel()
             ->update(array('IS_READ' => 1), 'USER_SN = ' . $userSN);
        
        $this->getModel('User')->update(array('INFORMED_UNREAD' => 0), 'SN = ' . $userSN);
    }
    
    /**
     * 推播訊息
     * 
     */
    public function pullMessageAction()
    {
        error_reporting(0);
        
        set_time_limit(120);
        Zend_Session::writeClose();
        
        $model  = $this->getModel();
        $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
        
        while (true) {
            
            $quantity = $model->getUnreadQuantity($userSN);
            $unreadCount = $this->_getInformedUserUnreadCount();

            /*
             * 未讀數量為 0 時 以及，未讀訊息等於已通知未讀的訊息數量相等時，不做通知
             */
            if($quantity != 0 && $quantity != $unreadCount) {
                
                # 更新已通知的未讀訊息數量
                $this->getModel('User')->updateInformedCount($quantity, $userSN);
                
                break;
            }
            
            sleep(5);
        }
        
        $this->_helper->json(array(
        	'count' => $quantity
        ));
    }
    
    /**
     * 取得通知訊息
     * 
     */
    public function getInformAction()
    {
        $model  = $this->getModel();
        $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
        
        $quantity = $model->getUnreadQuantity($userSN);
        $unreadCount = $this->_getInformedUserUnreadCount();
        
        if($quantity != 0 && $quantity != $unreadCount) {
                
            # 更新已通知的未讀訊息數量
            $this->getModel('User')->updateInformedCount($quantity, $userSN);
        }
        
        echo $quantity;
        exit;
    }
    
    /**
     * 取得已通知的未讀訊息數量
     * 
     */
    protected function _getInformedUserUnreadCount()
    {
        $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
        $row = $this->getModel('User')->fetchRowByPrimary($userSN);

        return $row['INFORMED_UNREAD'];
    }
}
?>