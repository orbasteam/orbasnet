<?php
/**
 * Datetime picker
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_FormDatetime extends Zend_View_Helper_Abstract
{
    public function formDatetime($name, $value = null, $attr = array())
    {
        $this->_generateJavascript($name);
        
        if($value !== null) $date = date('Y-m-d', strtotime($value));
        
        # datepicker
        $input = $this->view->formText(
            $name . '_date', 
            isset($date) ? $date : null,
            isset($attr['input']) ? $attr['input'] : null
        );
        
        # time
        $timeSelect = $this->_getTimeSelect(
            $name, $value, isset($attr['select']) ? $attr['select'] : null);
        
        $hidden = $this->view->formHidden($name, $value);
        
        return $input . $timeSelect . $hidden;
    }

    /**
     * time picker
     * 
     * @param string $name
     * @param string $value
     * @param array  $attr
     */
    protected function _getTimeSelect($name, $value, $attr)
    {
        $hours = array('' => '');
        for($i=0; $i<24; $i++) {
            $i = sprintf('%02d', $i);
            $hours[$i] = $i;
        }
        
        $minutes = array('' => '');
        for($i=0; $i<60; $i++) {
            $i = sprintf('%02d', $i);
            $minutes[$i] = $i;
        }
        
        return $this->view->formSelect($name . '_hour', (int)($value ? date('H', strtotime($value)) : null), $attr, $hours) . '：' . 
               $this->view->formSelect($name . '_min',  (int)($value ? date('i', strtotime($value)) : null), $attr, $minutes);
    }
    
    protected function _generateJavascript($name)
    {
        $js = <<<JS
        $(function(){
            $("#{$name}_date").datepicker({
                format : 'yyyy-mm-dd'
            }).on('changeDate', function(){
                $(this).change();
            });;
            
            $("#{$name}_date, #{$name}_hour, #{$name}_min").change(function(){
                var _value = $("#{$name}_date").val() + ' ' + $("#{$name}_hour").val() + ':' + $("#{$name}_min").val() + ':00' ;
                $("#{$name}").val(_value);
            });
        });
JS;
        $this->view->headScript()->appendScript($js);
    }
}
?>