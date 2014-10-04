<?php
/**
 * 產生多語系輸入欄位抽象層
 * 
 * @author Flash
 *
 */
abstract class Orbas_View_Helper_MultiLangAbstract extends Zend_View_Helper_Abstract
{
    /**
     * 取得語系
     */
    protected function _getLocales()
    {
    	$enumProvider = Zend_Controller_Action_HelperBroker::getStaticHelper('EnumProvider');
    	return $enumProvider->enum('WebsiteLang');
    }
}
?>