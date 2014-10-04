<?php

require_once 'AbstractController.php';

/**
 * 使用者資料修改
 * 
 * @author Ivan
 *
 */
class ProfileController extends AbstractController
{
    public function indexAction()
    {
        $this->getMessageFromSession();
        
        if(!isset($this->view->data)) {
            $user = Orbas_Auth::getUser(ROLE_MEMBER);
            $this->view->data = new Orbas_DataObject($user);
        }
    }
    
    /**
     * 儲存個人資料
     * 
     */
    public function saveAction()
    {
        try {
            
            $post = $this->getRequest()->getPost();
            
            if(!empty($post['PASSWORD'])) {
                if($post['PASSWORD'] != $post['CONFIRM_PASSWORD']) {
                    throw new Exception('密碼不一致');
                }
            } else {
                unset($post['PASSWORD']);
            }
            
            $post['SN'] = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
            $this->getModel()->updateByPrimary($post);
            
            $this->_updateSession($post['SN']);
            
            $this->setMessageIntoSession('更新完成', self::MESSAGE_SUCCESS);
            $this->_helper->redirector('index');
            
        } catch (Exception $e) {
            $this->view->data = new Orbas_DataObject($post);
            $this->_handelErrorMessage($e);
            $this->_forward('index');
        }
    }
    
    /**
     * 切換桌面通知
     * 
     */
    public function switchNotificationAction()
    {
        try {
            
            $switch = $this->getParam('switch');
            $user = $this->getUser();
            
            $row = $this->getModel()->fetchRowByPrimary($user['SN']);
            $row->NOTIFICATION = $row['NOTIFICATION'] ? 0 : 1;
            $row->save();
            $row->refresh();
            
            $this->_updateSession($user['SN']);
            
            echo $row->NOTIFICATION;
            
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        
        exit;
    }
    
    /**
     * 上傳圖片
     * 
     */
    public function uploadImageAction()
    {
        try {
            $user = Orbas_Auth::getUser();
            $productSN = $this->getParam('productSN');
            
            $path = APPLICATION_PATH . '/../public/upload/profile/';
            if(!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            
            $primaryKey = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
            
            $options = array(
                'name' => 'AVATAR',
                'destination' => $path,
                'primaryKey'  => $primaryKey,
                'fileName'    => sprintf('%04d', $primaryKey)  
            );
            $image = new Orbas_Image($this, $options);
            $image->upload();
            
            $this->_updateSession($primaryKey);
            
            $this->_helper->json(array(
            	'error' => 0
            ));
            
        } catch (Orbas_Form_Validator_Exception $e) {
		
		    $this->_helper->json(array(
		        'error' => 1,
		        'message' => $e->getMessages()
		    ));
		    
        } catch (Exception $e) {
            $this->_helper->json(array(
            	'error'   => 1,
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * 更新Session
     * 
     * @param integer $userSN
     */
    protected function _updateSession($userSN)
    {
        $data = $this->getModel()->fetchRowByPrimary($userSN)->toArray();
        $this->getHelper('Auth')->setUser($data, ROLE_MEMBER);
    }
}
?>