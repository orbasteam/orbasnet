<?php
class Orbas_List_DataAdapter_Enum extends Orbas_List_DataAdapter
{
    /**
     * 
     * @var bool
     */
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
		
		if(isset($this->_data[$key])) {
		    return $this->_data[$key];
		}
		
		return '';
	}
	
	/**
	 * 產生鍵與值對應陣列
	 * 
	 */
	protected function _createKeyValuePairs()
	{
	    $source = $this->_config->source;
	    
	    if(!Zend_Registry::isRegistered($source)){
	        require APPLICATION_PATH . '/../data/enums/global.php' ;
	    }
	    
	    $this->_data = Zend_Registry::get($source);
	}
}

?>