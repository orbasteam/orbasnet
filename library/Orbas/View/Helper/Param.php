<?php
/**
 * 從view 取得param
 * 等同於從controller的 $this->getRequest()->getParam($key, $default);
 * 
 * @author Ivan
 * @subpackage View
 */
class Orbas_View_Helper_Param extends Zend_View_Helper_Abstract
{
    public function param($key, $default = null)
    {
        return Zend_Controller_Front::getInstance()->getRequest()->getParam($key, $default);
    }
}
?>