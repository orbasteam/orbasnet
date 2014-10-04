<?php
/**
 * 語系下拉選單切換器
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_LangChanger extends Zend_View_Helper_FormSelect
{
    /**
     * 產生語系切換的下拉選單
     * 
     * 
     * @param string $default
     */
    public function langChanger($default = 'zh_TW')
    {
        $this->view
             ->headScript()
             ->appendScript($this->_generateScript(__FUNCTION__));
        
        return $this->formSelect(__FUNCTION__, $default, array('class' => 'chosen'), $this->_getLocales()); 
    }
    
    /**
     * 取得語系
     */
    protected function _getLocales()
    {
        $enumProvider = Zend_Controller_Action_HelperBroker::getStaticHelper('EnumProvider');
        return $enumProvider->enum('WebsiteLang');
    }
    
    /**
     * 
     * @param string $changerName
     */
    protected function _generateScript($changerName)
    {
        return <<<JS
        $(function(){
            $("#$changerName").change(function(){
                $("div[data-lang]").hide();
                $("div[data-lang='" + $(this).val() + "']").show();
            }).change();
        });
JS;
    }
}
?>