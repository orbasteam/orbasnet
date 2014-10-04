<?php
/**
 * 多語系序列化字串顯示
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_MultiLangString extends Zend_View_Helper_Abstract
{
    /**
     * 顯示多語系序列化字串
     * 
     * @param string $string
     * @param string $locale
     */
    public function multiLangString($string, $locale = null)
    {
        if($locale === null) {
            $locale = Orbas_Translate::getLocale();
        }

        $string = unserialize($string);
        if(isset($string[$locale])){
            return $string[$locale];
        }
        
        return null;
    }
}
?>