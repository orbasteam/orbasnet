<?php

/**
 * 訊息通知
 * 
 * @author Ivan
 *
 */
class Default_InformModel extends Orbas_Model_Abstract
{
    protected $_name = 'inform';
    
    /**
     * 訊息通知給所有使用者
     * 
     * @param integer $userSN   發送通知的使用者 (不需要列入被通知的對象)
     * @param integer $boardSN
     * @param string  $content
     */
    public function inform($userSN, $boardSN, $content)
    {
        $users = $this->_getUsers($userSN);
        
        foreach($users as $user) {
            $this->insert(array(
            	'USER_SN'  => $user,
                'SEND_USER_SN' => $userSN,
                'BOARD_SN' => $boardSN,
                'CONTENT'  => $content
            ));
        }
    }
    
    /**
     * 取得全部通知 (提供view使用)
     * 
     * @param integer $userSn
     */
    public function fetchForView($userSn, $limit = 5)
    {
        return $this->getAdapter()
                    ->select()
                    ->from($this->_name)
                    ->joinLeft('user', 'user.SN = inform.SEND_USER_SN', array('AVATAR', 'NAME'))
                    ->where('inform.USER_SN = ?', $userSn)
                    ->order('SN DESC')
                    ->limit($limit, 0)
                    ->query()
                    ->fetchAll();
    }
    
    /**
     * 取得未讀訊息數量
     * 
     * @param integer $userSN
     */
    public function getUnreadQuantity($userSN)
    {
        $select = $this->select()
                       ->from($this->_name, array('COUNT' => 'COUNT(*)'))
                       ->where('USER_SN = ?', $userSN)
                       ->where('IS_READ = 0');
        
        return $this->getAdapter()->fetchOne($select);
    }
    
    /**
     * 取得所有使用者
     * 
     * @param integer $exceptUser 排除的使用者
     */
    protected function _getUsers($exceptUser = 0)
    {
        return $this->getAdapter()
                    ->select()
                    ->from('user', array('SN'))
                    ->where('SN <> ?', $exceptUser)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
    }
}
?>