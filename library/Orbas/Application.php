<?php
class Orbas_Application extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 從真實路徑轉換為儲存在資料表的路徑
     * 
     * @param string $path
     * @param boolean $includeSlash
     */
    static public function transferToDbPath($path, $includeSlash = true)
    {
        $path = str_replace('\\', '/', $path);
        $path = rtrim(substr($path, strlen($_SERVER['DOCUMENT_ROOT'])), '/');
        
        return $includeSlash ? $path . '/' : $path;
    }
}
?>