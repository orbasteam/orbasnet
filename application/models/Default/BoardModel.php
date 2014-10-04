<?php
/**
 * 塗鴉牆
 * 
 * @author Ivan
 *
 */
class Default_BoardModel extends Orbas_Model_Abstract
{
    protected $_name = 'board';

    /**
     * 取得塗鴉牆列表留言
     * 
     * @param integer $count
     * @param integer $page
     */
    public function fetchBoard($count, $page = 0)
    {
        return $this->getAdapter()
                    ->select()
                    ->from($this->_name)
                    ->join('user', 'user.SN = board.USER_SN', array('NAME', 'AVATAR'))
                    ->where('board.BOARD_SN IS NULL')
                    ->where('board.REMOVED = 0')
                    ->limitPage($page, $count)
                    ->order('board.REPLY_DATETIME DESC')
                    ->query()
                    ->fetchAll();
    }
    
    /**
     * 取得留言的內容 (如果是回應文則取得被回應的文章)
     * 
     * @param integer $boardSN
     * @return mixed
     */
    public function fetchParentBoard($boardSN)
    {
        $data = $this->getAdapter()
                     ->select()
                     ->from($this->_name)
                     ->join('user', 'user.SN = board.USER_SN', array('NAME', 'AVATAR'))
                     ->where('board.REMOVED = 0')
                     ->where('board.SN = ?', $boardSN)
                     ->query()
                     ->fetch();
        
        if(!$data['BOARD_SN']) {
            return $data;
        }
        
        return $this->getAdapter()
                    ->select()
                    ->from($this->_name)
                    ->join('user', 'user.SN = board.USER_SN', array('NAME', 'AVATAR'))
                    ->where('board.REMOVED = 0')
                    ->where('board.SN = ?', $data['BOARD_SN'])
                    ->query()
                    ->fetch();
    }
    
    /**
     * 取得回應
     * 
     * @param integer $boardSN
     */
    public function fetchReply($boardSN)
    {
        return $this->getAdapter()
                    ->select()
                    ->from($this->_name)
                    ->join('user', 'user.SN = board.USER_SN', array('NAME', 'AVATAR'))
                    ->where('board.BOARD_SN = ?', $boardSN)
                    ->where('board.REMOVED = 0')
                    ->order('board.SN ASC')
                    ->query()
                    ->fetchAll();
    }
    
    /**
     * 取得回應數
     * 
     * @param integer|array $boardSn
     */
    public function fetchReplyCount($boardSn)
    {
        if(is_int($boardSn)) {
            $boardSn = (array)$boardSn;
        }
        
        $select = $this->select()
                       ->from($this->_name, array('BOARD_SN', 'COUNT' => 'COUNT(BOARD_SN)'))
                       ->where('BOARD_SN IN (?)', $boardSn)
                       ->where('REMOVED = 0')
                       ->group('BOARD_SN');
        
        return $this->getAdapter()->fetchPairs($select);
    }
}
?>