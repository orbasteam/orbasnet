<?php
/**
 * Number format
 * 
 * @author Ivan
 *
 */
class Orbas_List_DataAdapter_NumberFormat extends Orbas_List_DataAdapter
{
    public function getText ($key)
    {
        if($key == null) {
            return null;
        }
        
    	return number_format($key);
    }
}
?>