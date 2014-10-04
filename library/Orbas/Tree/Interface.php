<?php
interface Orbas_Tree_Interface
{
    /**
     * 當需要取得Tree時，提供自訂條件時的事件
     * 
     * @param Zend_Db_Select $select
     * @param Orbas_Tree $tree
     */
    public function onTreeSearch(Zend_Db_Select $select, Orbas_Tree $tree);
}
?>