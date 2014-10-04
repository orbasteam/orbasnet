<?php

require_once 'AbstractController.php';

/**
 * 塗鴉牆文章回應
 * 
 * @author Ivan
 *
 */
class ReplyController extends AbstractController
{
    protected $_modelName = 'Board';
    
    /**
     * 回應列表
     * 
     */
    public function listAction()
    {
        $this->_helper->layout()->disableLayout();
        $rows = $this->getModel()->fetchReply($this->getParam('board_sn'));
        
        $like = array();
        $userLike = array();
        
        if($rows) {

            $boardSns = array_column($rows, 'SN');
            
            # 取得讚及爛
            $like = $this->getModel('Like')->fetchAllQuantityByBoard($boardSns);
            
            # 使用者點的讚及爛
            $userLike = $this->getModel('Like')->fetchAllByUser(Orbas_Auth::getUserInfo('SN', ROLE_MEMBER), $boardSns);
        }
        
        $this->view->rows = $rows;
        $this->view->like = $like;
        $this->view->userLike = $userLike;
        
        sleep(1);
    }
}
?>