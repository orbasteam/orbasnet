<?php
/**
 * 處理多語系欄位，並且將資料序列化
 * 
 * @author Ivan
 *
 */
class Orbas_Form_Filter_MultiLang extends Orbas_Form_Filter_Abstract
{
	/* (non-PHPdoc)
	 * @see Orbas_Filter_Abstract::filter()
	 */
	public function filter($field) 
	{
	    $multiLangFields = array();
		foreach($this->_data as $key => $data) {
		    
		    $isExist = strpos($key, $field . '_');
		    if($isExist !== false && $isExist === 0){
		        $langKey = substr($key, strlen($field)+1);
		        $multiLangFields[$langKey] = $data;
		    }
		}
		
		/*
		 * 驗證是否有資料，以避免驗證欄位時，
		 * 因為已經序列化，造成驗證判斷錯誤
		 */
		if($multiLangFields) {
		    
		    foreach($multiLangFields as $text) {
		        if($text != null) {
		            $this->_data->set($field, serialize($multiLangFields));
		        }
		    }
		}
	}
}
?>