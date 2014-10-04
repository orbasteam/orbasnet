<?php

/**
 * 圖片置換
 * 
 * @author Ivan
 *
 */
class Orbas_ContentTransfer_Img implements Orbas_ContentTransfer_Interface
{
	/* (non-PHPdoc)
	 * @see Orbas_ContentTranfer_Interface::tranfer()
	 */
	public function tranfer($content) 
	{
	    $path = $this->_movePicture();
        if(count($path)) {
            $content .= "\n";
            foreach($path as $value) {
                $content .= '<img src="' . $value . '" /> ' ;
            }
            
            $message  = Orbas_Auth::getUserInfo('NAME', ROLE_MEMBER) . ' 新增了' . count($path) . '張圖片：「';
            $message .= mb_substr($this->_content, 0, 50, 'utf8') . '」';
            $this->_setInformMessage($message);
            
            return $content;
        }
        
        return false;
	}
	
	/**
	 * 通知訊息
	 * 
	 * @var string
	 */
	protected $_informMessage;
	
	/**
	 * 設定通知訊息
	 * 
	 * @param string $message
	 */
	protected function _setInformMessage($message)
	{
	    $this->_informMessage = $message;
	}
	
    /**
     * 
     * @return array path
     */
    protected function _movePicture()
    {
        $path = array();
        $tempPath = $this->getTempPath();
        $tempRealPath = APPLICATION_PATH . '/../public/' . ltrim($tempPath, '/');
        if(is_dir($tempRealPath)) {
            
            $uploadPath = '/upload/picture/' . Orbas_Auth::getUserInfo('SN', ROLE_MEMBER) . '/' . uniqid();
            $directory  = new DirectoryIterator($tempRealPath);
            foreach($directory as $file) {
                if(!$file->isDot() && !$file->isDir()) {
                    $source = $file->getRealPath();
                    $dest = APPLICATION_PATH . '/../public' . $uploadPath . '/' ;

                    if(!is_dir($dest)) {
                        mkdir($dest, 0777, true);
                    }
                    
                    copy($source, $dest . $file->getFilename());
                    unlink($source);
                    
                    $path[] = $uploadPath . '/' . $file->getFilename();
                } 
            }
        }
        
        return $path;
    }
    
    /**
     * 暫存檔路徑
     *
     * @return string
     */
    public function getTempPath()
    {
    	$path = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('tempPath') . session_id();
    	return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }
    
	/* (non-PHPdoc)
	 * @see Orbas_ContentTranfer_Interface::getInformMessage()
	 */
	public function getInformMessage() 
	{
	    return $this->_informMessage;
	}
}
?>