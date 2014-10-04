<?php
/**
 * 讚與爛
 * 
 * @author Ivan
 *
 */
class Default_LikeModel extends Orbas_Model_Abstract
{
    protected $_name = 'like';
    
    /**
     * 取得讚的數量
     * 
     * @param integer $boardSN
     * @param integer $type
     */
    public function fetchCountByBoard($boardSN, $type = LIKE)
    {
        $select = $this->select()
                       ->from($this->_name, array('COUNT' => 'COUNT(*)'))
                       ->where('TYPE_KEY = ?', $type)
                       ->where('BOARD_SN = ?', $boardSN);
                       
        return $this->getAdapter()->fetchOne($select);
    }
    
    /**
     * 取得讚及爛的數量
     * 
     * @param integer|array $boardSns
     * @return array
     */
    public function fetchAllQuantityByBoard($boardSns)
    {
        if(!is_array($boardSns)) {
            $boardSns = (array)$boardSns;
        }
        
        $rows = $this->fetchAll('BOARD_SN IN(' . implode(',', $boardSns) . ')');
        $result = array();
        
        foreach($rows as $row) {
            @$result[$row['BOARD_SN']][$row['TYPE_KEY']] += 1;
        }
        
        return $result;
    }
    
    /**
     * 取得使用者點的讚及爛
     * 
     * @param integer $userSN
     * @param integer|array $boardSns
     */
    public function fetchAllByUser($userSN, $boardSns)
    {
        if(!is_array($boardSns)) {
            $boardSns = (array)$boardSns;
        }
        
    	$rows = $this->fetchAll('USER_SN = ' . $userSN . ' AND BOARD_SN IN(' . implode(',', $boardSns) . ')');
    	$result = array();
    	
    	foreach($rows as $row) {
    	    $result[$row['BOARD_SN']][$row['TYPE_KEY']] = 1;
    	}
    	
    	return $result;
    }
    
    /**
     * 取得點讚的人
     * 
     * @param integer $boardSN
     */
    public function fetchLikeByBoard($boardSN)
    {
        $select = $this->getAdapter()
                       ->select()
                       ->from($this->_name, array())
                       ->join('user', 'user.SN = like.USER_SN', array('SN', 'NAME'))
                       ->where('like.BOARD_SN = ?', $boardSN)
                       ->where('like.TYPE_KEY = ?', LIKE);
        
        return $this->getAdapter()->fetchPairs($select);
    }
    
    /**
     * 取得點爛的人
     * 
     * @param integer $boardSN
     */
    public function fetchDislikeByBoard($boardSN)
    {
        $select = $this->getAdapter()
                       ->select()
                       ->from($this->_name, array())
                       ->join('user', 'user.SN = like.USER_SN', array('SN', 'NAME'))
                       ->where('like.BOARD_SN = ?', $boardSN)
                       ->where('like.TYPE_KEY = ?', DISLIKE);
        
        return $this->getAdapter()->fetchPairs($select);
    }
}
?>