<?php
/**
 * 字串加密 (單向，不可逆)
 * 
 * @author Ivan
 *
 */
class Orbas_Helper_Crypt extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 
     * @param string $input
     * @param string $salt
     */
    public function crypt($input, $salt)
    {
        return crypt($input, $this->_getMd5HashingSalt($salt));
    }
    
    /**
     * 取得作為md5 hash 的 12位字元salt值
     * 
     * @param unknown $string
     */
    protected function _getMd5HashingSalt($string)
    {
        $string = md5($string);
        
        return '$1$' . substr($string, 0, 9);
    }
}
?>