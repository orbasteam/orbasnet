<?php
/**
 * 投票
 * @author Dennis
 */
class VoteController extends AbstractController
{
    /**
     * 發起投票
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
     * 投票動作
     */
    public function voteAction()
    {
        
    }
    
    /**
     * 增加投票選項
     */
    public function addVoteObjectAction()
    {
        
    }
    
    /**
     * 移除投票選項
     */
    public function removeVoteObjectAction()
    {
        
    }
    
    public function listAction()
    {
        
    }

    /**
     * 目前參與的投票 List
     */
    public function activeListAction()
    {
        
    }
    
    /**
     * 歷史投票 List
     */
    public function historyListAction()
    {
        
    }
    
    public function detailAction()
    {
        
    }
}
?>