<?php
/**
 * 多語系Text輸入欄位，需搭配 LangChanger Helper 做切換
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_MultiLangText extends Orbas_View_Helper_MultiLangAbstract
{
    public function MultiLangText($name, $value = null, $attribs = null)
    {
        if($value !== null) {
            $value = unserialize($value);
        }
        
        $html = '';
        foreach(array_keys($this->_getLocales()) as $locale) {
            
            $html .= '<div data-lang="' . $locale . '" class="hide">';
            $html .= $this->view->formText($name . '_' . $locale, @$value[$locale], $attribs);
            $html .= '</div>';
        }
        
        return $html;
    }
}
?>