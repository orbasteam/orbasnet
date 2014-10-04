<?php
/**
 * 會員
 * 
 * @author Ivan
 *
 */
class Default_UserModel extends Orbas_Model_Abstract
{
	protected $_name = 'user';
	
	/**
	 * 更新已通知的訊息數量
	 * 
	 * @param integer $quantity
	 * @param integer $userSN
	 */
	public function updateInformedCount($quantity, $userSN)
	{
	    $user = $this->fetchRowByPrimary($userSN);
	    
	    if($user['INFORMED_UNREAD'] != $quantity) {
	        $user->INFORMED_UNREAD = $quantity;
	        $user->save();
	    }
	}
	
	/**
	 * 更新使用者最後線上時間
	 * 
	 * @param integer $userSN
	 */
	public function updateOnlineTime($userSN)
	{
	    $this->update(array(
	    	'LAST_ONLINE_TIME' => time()
	    ), 'SN = ' . $userSN);
	}
}
?>