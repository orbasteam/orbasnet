<?php
/**
 * 
 * 樹狀下拉選擇器使用的 Controller
 * 
 * @author Ivan
 *
 */
abstract class Orbas_View_Helper_Controller_ChainSelector extends Orbas_Controller_Action
{
    /**
     * 選單顯示的名稱
     * 
     * @var string
     */
    protected $_field = 'NAME';
    
    /**
     * 取得樹狀選單集合
     *
     */
    public function ajaxGetOptionsAction()
    {
	    # 父節點，使用此鍵值取得下層資料
		$parent = $this->getParam('id', 0);
		
		$select = $this->getModel()->select();
		$select->where('PARENT_SN = ?', $parent);
		$this->_customSelect($select);
		
		$result = array();
		$rows = $select->query()->fetchAll();
		foreach($rows as $row) {
		    $result[] = array(
		      'name'  => $this->_onCreateField($row[$this->_field]),
		      'value' => $row['SN']
		    );
		}
		
		$this->_helper->json($result);
    }
    
    /**
     * 產生欄位名稱時的事件
     * 
     * @param string $field
     */
    protected function _onCreateField($field)
    {
        // Do nothing
    }
    
    /**
     * 提供子類別複寫過濾條件
     * 
     * @param Zend_Db_Select $select
     */
    protected function _customSelect(Zend_Db_Select $select)
    {
        // Do nothing
    }
}
?>