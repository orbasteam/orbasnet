<?php
/**
 * 
 * 多語系模組
 * 
 * @author Ivan
 *
 */
class Orbas_List_DataAdapter_MultilingualModel extends Orbas_List_DataAdapter_Model
{
    public function getText ($key)
    {
    	$text = parent::getText($key);
    	
    	$locale   = Orbas_Translate::getLocale();
    	$tmpArray = unserialize($text);
    	return $tmpArray[$locale];
    	
    }
}
?>