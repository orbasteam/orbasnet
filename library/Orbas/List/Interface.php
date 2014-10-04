<?php
/**
 * 提供後台List使用的介面
 * 
 * @author Ivan
 *
 */
interface Orbas_List_Interface
{
    /**
     * 提供產生list前自訂的搜尋條件
     * 
     * @param Zend_Db_Select $select
     * @param Orbas_List $list
     */
    public function onListSearch(Zend_Db_Select $select, Orbas_List $list);
    
    /**
     * 提供List Helper 物件初始化時設定參數使用
     * 
     * @param Orbas_List $list
     */
    public function onListInit(Orbas_List $list);
}
?>