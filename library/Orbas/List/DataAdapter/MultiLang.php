<?php
/**
 * 多語系顯示
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_List_DataAdapter_MultiLang extends Orbas_List_DataAdapter
{
    public function getText ($key)
    {
    	$locale = Orbas_Translate::getLocale();
    	$data   = unserialize($key);
    	
    	return $data[$locale];
    }
}
?>