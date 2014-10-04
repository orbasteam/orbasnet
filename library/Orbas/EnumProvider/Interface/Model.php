<?php
/**
 * 使用Model集合提供器時，實作的介面
 * 
 * @author Ivan
 *
 */
interface Orbas_EnumProvider_Interface_Model
{
    /**
     * 產生集合時過濾的條件
     * 
     * @param string $source
     * @param Zend_Db_Select $select
     */
    public function onModelEnumCreate($source, Zend_Db_Select $select);
}
?>