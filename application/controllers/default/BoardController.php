<?php
require_once 'AbstractController.php';

/**
 * 
 * @author Ivan
 *
 */
class BoardController extends AbstractController
{
    /**
     * 每頁幾筆
     * 
     * @var integer
     */
    protected $_rowsPerPage = 10;
    
    public function listAction()
    {
        $this->_helper->layout()->disableLayout();
        $rows = $this->getModel('Board')
                     ->fetchBoard($this->_rowsPerPage, $this->getParam('page', 1));
        
        $this->view->rows = $rows;

        $replys   = array();
        $like     = array();
        $userLike = array();
        
        if($rows) {

            $boardSns = array_column($rows, 'SN');
            
            # 取得回應
            $replys = $this->getModel()->fetchReplyCount($boardSns);

            # 取得讚及爛
            $like = $this->getModel('Like')->fetchAllQuantityByBoard($boardSns);
            
            # 使用者點的讚及爛
            $userLike = $this->getModel('Like')->fetchAllByUser(Orbas_Auth::getUserInfo('SN', ROLE_MEMBER), $boardSns);
        }
        
        $this->view->replys = $replys;
        $this->view->like = $like;
        $this->view->userLike = $userLike;
    }
    
    /**
     * 留下訊息
     * 
     */
    public function submitAction()
    {
        try {
            
            if(!$this->getRequest()->isPost()) {
                $this->_pageNotFound();
            }
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $post = $this->getRequest()->getPost();
            $dateTime = date('Y-m-d H:i:s');
            
            # 修改回應時間
            if(isset($post['BOARD_SN'])) {
                $this->getModel()->updateByPrimary(array(
                	'SN' => $post['BOARD_SN'],
                    'REPLY_DATETIME' => $dateTime
                ));
            }
            
            $post['CONTENT'] = $this->view->escape($post['CONTENT']);
            $orgContent = $post['CONTENT'];

            $contentTranfer = new Orbas_ContentTransfer();
            $newContent = $contentTranfer->tranfer($orgContent);
            if($newContent) {
                $post['CONTENT'] = $newContent;
            }
            
            $post['USER_SN']  = Orbas_Auth::getUserInfo('SN', ROLE_MEMBER);
            $post['DATETIME'] = $dateTime;
            $post['IP'] = $this->getRequest()->getClientIp();
            $post['REPLY_DATETIME'] = date('Y-m-d H:i:s');
            
            $sn = $this->getModel()->append($post);

            /*
             * 通知所有使用者
             */
            if($newContent) {
                
            } else {
                $content = mb_substr($post['CONTENT'], 0, 50, 'utf8');
            }
            if(isset($path) && count($path)) {
                $content  = Orbas_Auth::getUserInfo('NAME', ROLE_MEMBER) . ' 新增了' . count($path) . '張圖片：「';
                $content .= mb_substr($orgContent, 0, 50, 'utf8') . '」';
                
            } else if(isset($youtubeCount) && $youtubeCount) {
                $content = Orbas_Auth::getUser('NAME', ROLE_MEMBER) . ' 新增了1部影片';
            } else {
                
            }
            
            $this->getModel('Inform')->inform($post['USER_SN'], $sn, $content);
            
            $db->commit();
            $this->_ajaxSuccess();
            
        } catch (Exception $e) {
            $db->rollBack();
            $this->_ajaxError($e->getMessage());
        }
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
     * 留言刪除
     * 
     */
    public function removeAction()
    {
        try {
            
            if(!$this->getRequest()->isDelete()) {
                $this->_pageNotFound();
            }
            
            $rawBody = $this->getRequest()->getRawBody();
            $param = array();
            parse_str($rawBody, $param);
            
            $data = $this->getModel('Board')->fetchRowByPrimary($param['board_sn']);
            if($data) {
                $data->REMOVED = 1;
                $data->save();
            }
            
            $this->_ajaxSuccess();
            
        } catch (Exception $e) {
            $this->_ajaxError($e->getMessage());
        }
    }
    
    /**
     * 將刪除的留言復原
     * 
     */
    public function recoveryAction()
    {
        try {
            
            if(!$this->getRequest()->isPost()) {
                $this->_pageNotFound();
            }
            
            $data = $this->getModel('Board')->fetchRowByPrimary($this->getParam('board_sn'));
            if($data) {
                $data->REMOVED = 0;
                $data->save();
            }
            
            $this->_ajaxSuccess();
            
        } catch (Exception $e) {
            $this->_ajaxError($e->getMessage());
        }
    }
    
    /**
     * 留言內容
     * 
     */
    public function detailAction()
    {
        $data = $this->getModel()->fetchParentBoard($this->getParam('sn'));
        
        if(!$data) {
            throw new Exception('文章不存在');
        }
        
        $this->view->data = $data;
        
        # 回應文
        $this->view->replys = $this->getModel()->fetchReply($data['SN']);

        $boardSns = array_merge(array_column($this->view->replys, 'SN'), (array)$data['SN']);
        
        # 取得讚及爛
        $like = $this->getModel('Like')->fetchAllQuantityByBoard($boardSns);
        
        # 使用者點的讚及爛
        $userLike = $this->getModel('Like')->fetchAllByUser(Orbas_Auth::getUserInfo('SN', ROLE_MEMBER), $boardSns);
        
        $this->view->like = $like;
        $this->view->userLike = $userLike;
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
    
    protected function _replaceUrl($content)
    {
        return preg_replace(
          "#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
          "'<a href=\"$1\" target=\"_blank\" class=\"btn btn-link\">$1</a>$4'", $content);
    }
}
?>