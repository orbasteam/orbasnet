<?php
class Default_UserSessionModel extends Orbas_Model_Abstract
{
    protected $_name = 'user_session';
    
    /**
     * 取得使用者
     * 
     * @param integer $sn
     * @param string  $sid
     * @param string  $role
     * @return mixed
     */
    public function fetchUser($sn, $sid, $role)
    {
        $row = $this->select()
                    ->where('SN = ?', $sn)
                    ->where('SESSION_ID = ?', $sid)
                    ->where('ROLE = ?', $role)
                    ->query()
                    ->fetch();
        
        if($row) {
            $user = $this->getAdapter()
                         ->select()
                         ->from('user')
                         ->where('SN = ?', $row['USER_SN'])
                         ->query()
                         ->fetch();
            
            if($user) {
                return $user;
            }
        }
        
        return null;
    }
}
?>