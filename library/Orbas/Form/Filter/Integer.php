<?php
/**
 * 過濾非整數欄位
 * 
 * @author Flash
 *
 */
class Orbas_Form_Filter_Integer extends Orbas_Form_Filter_Abstract
{
    public function filter ($field)
    {
    	if($this->_data->$field === ''){
    	    $this->_data->set($field, null);
    	} else if($this->_data->$field !== null) { 
    		$this->_data->set($field, intval($this->_data->$field));
    	}
    }
}
?>