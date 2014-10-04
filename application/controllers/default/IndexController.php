<?php

require_once 'AbstractController.php';

class IndexController extends AbstractController
{
    public function indexAction()
    {
        # 更新已通知訊息的數量
        $this->_updateInformedMessageCount();
        
        # 上傳的圖片
        $this->view->pictures = $this->_getTempPictures();
        
        $this->sessionSet('test', 'test');
    }
    
    /**
     * 線上人數
     * 
     */
    public function onlineMemberAction()
    {
        $this->_helper->layout()->disableLayout();
        
        $userModel = $this->getModel('User');
        $rows = $userModel->fetchAll('LAST_ONLINE_TIME >= ' . (time() - 20));
        
        $result = array();
        foreach($rows as $row) {
            
            $result[$row['SN']] = array(
            	'name' => $row['NAME'],
                'avatar' => $row['AVATAR']
            );
        }
        
        $this->view->rows = $result;
    }
    
    /**
     * 更新已通知訊息的數量
     * 
     */
    protected function _updateInformedMessageCount()
    {
        $userSN = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
        $quantity = $this->_getUnreadQuantity();
        $this->getModel('User')->updateInformedCount($quantity, $userSN);
    }
    
    /**
     * 更新記錄
     */
    public function changeLogAction()
    {
        $this->view->data= $this->getChangeLog();
    }
    
    protected function _getTempPictures()
    {
        $tempPath = APPLICATION_PATH . '/../public/' . ltrim($this->getTempPath(), DIRECTORY_SEPARATOR);
        $tempPath = realpath(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $tempPath));
        
        if(is_dir($tempPath)) {
            
            $pictures  = array();
            $directory = new DirectoryIterator($tempPath);
            foreach($directory as $file) {
                if(!$file->isDot() && !$file->isDir()) {
                    $pictures[] = str_replace(DIRECTORY_SEPARATOR, '/', $this->getTempPath() . DIRECTORY_SEPARATOR . $file->getFilename());
                } 
            }
            
            return $pictures;
        }
        
        return array();
    }
    
    /**
     * 上傳圖片
     * 
     */
    public function uploadImageAction()
    {
        try {
            
        	$updatePath = ltrim($this->getTempPath(), DIRECTORY_SEPARATOR);
        	 
        	$options = array(
    			'destination' => APPLICATION_PATH . '/../public/' . $updatePath,
    			'dbPath'      => $updatePath,
    			'updateDb'    => false,
        	    'fileName'    => md5($_FILES['file']['name'])
        	);
        	 
        	$image = new Orbas_Image($this, $options);
        	$image->setName('file')
        	      ->setWidth(1920)
        	      ->setHeight(1080)
        	      ->setKeepRatio(true)
        	      ->upload();
        
        	$this->_helper->json(array(
    			'error' => 0,
    			'file'  => $image->getDisplayFilePath()
        	));
        	 
        } catch (Exception $e) {
        	$this->_helper->json(array(
        		'error'   => 1,
        		'message' => $e->getMessage()
        	));
        }
    }
    
    public function removePictureAction()
    {
        try {
            
            if(!$this->getRequest()->isDelete()) {
                $this->_pageNotFound();
            }
            
            $rawBody = $this->getRequest()->getRawBody();
            $param = array();
            parse_str($rawBody, $param);
            
            $path = APPLICATION_PATH . '/../public/' . trim($this->getTempPath(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $param['filename'];
            if(!is_file($path)) {
                throw new Exception('檔案不存在');
            } 
            
            unlink($path);
            
            $this->_helper->json(array(
            	'error' => 0
            ));
            
        } catch (Exception $e) {
            $this->_helper->json(array(
            	'error' => 1,
                'message' => $e->getMessage()
            ));
        }
    }
    
    /**
     * 暫存檔路徑
     * 
     * @return string
     */
    public function getTempPath()
    {
        $path = $this->getInvokeArg('bootstrap')->getOption('tempPath') . session_id();
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }
}
