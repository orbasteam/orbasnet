<?php
class Orbas_List_DataAdapter_Model extends Orbas_List_DataAdapter
{
    const DEFAULT_GLUE = ',';
    
    protected $_flag = false;
    
	/* (non-PHPdoc)
	 * @see Orbas_List_DataAdapter::getText()
	 */
	public function getText($key) 
	{
	    if(!$this->_flag) {
	    	$this->_createKeyValuePairs();
	    	$this->_flag = true;
	    }
	    
	    if($key === null) {
	        return null;
	    }
	    
	    return $this->_data[$key];
	}
	
	/**
	 * 產生鍵與值對應陣列
	 *
	 */
	protected function _createKeyValuePairs()
	{
	    if(!$this->_foreignValue) {
	        return;
	    }
	    
		$source = $this->_config->source;
		$field  = $this->_config->field;
		
		if($field instanceof Zend_Config) {
		    $field = $field->toArray();
		}

		$column = array_merge((array)$field, array('SN'));
		
		$model = Orbas_Model_Broker::get($source);
		$rows  = $model->select()
		               ->from($model->info(Zend_Db_Table::NAME), $column)
		               ->where('SN IN (?)', $this->_foreignValue)
		               ->query()
		               ->fetchAll();

		if(is_string($field)) {
		    
		    foreach($rows as $row) {
		    	$this->_data[$row['SN']] = $row[$field];
		    }
		    
		} else if(is_array($field)) {
		    
		    /*
		     * 顯示於列表的格式
		     */
		    $format = null;
		    if(isset($this->_config->format)) {
		    	$format = $this->_config->format;
		    } else {

		        $format = '';
		        for( $i=0; $i < count($field); $i++) {
		            $format .= '%s' . self::DEFAULT_GLUE;
		        }
		        $format = rtrim($format, self::DEFAULT_GLUE);
		    }
		    
		    foreach($rows as $row) {
		        $args = array();
		        foreach($field as $value) {
		            $args[] = $row[$value];
		        }
		        $this->_data[$row['SN']] = vsprintf($format, $args);
		    }
		    
		}
	}
}

?>