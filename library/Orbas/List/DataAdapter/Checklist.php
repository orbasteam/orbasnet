<?php
/**
 * Checklist
 * 
 * @author Ivan
 *
 */
class Orbas_List_DataAdapter_Checklist extends Orbas_List_DataAdapter
{
    public function getText ($key)
    {
    	if($key) {
    	    return '<div align="center"><i class="icon-ok"></i></div>';
    	}
    	
    	return null;
    }
}
?>