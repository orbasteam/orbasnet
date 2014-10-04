<?php
/**
 * 多語系Textarea輸入欄位，需搭配 LangChanger Helper 做切換
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_MultiLangTextarea extends Orbas_View_Helper_MultiLangAbstract
{
    public function multiLangTextarea($name, $value = null, $attribs = null)
    {
        if($value !== null) {
        	$value = unserialize($value);
        }
        
        $html = '';
        foreach(array_keys($this->_getLocales()) as $locale) {
        
        	$html .= '<div data-lang="' . $locale . '" class="hide">';
        	$html .= $this->view->formTextarea($name . '_' . $locale, @$value[$locale], $attribs);
        	$html .= '</div>';
        }
        
        return $html;
    }
    
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