<?php
/**
 * 
 * @author Ivan
 *
 */
abstract class Orbas_Controller_Tree extends Orbas_Controller_Workbench implements 
Orbas_List_Interface
{
    const TREE_NODE_NAME = 'treeNodeField';  # 顯示樹狀節點的欄位名稱
    
    static protected $_initTreeOptions = array(
    	self::TREE_NODE_NAME => 'NAME'
    );
    
    public function preDispatch()
    {
        $this->view->caption = $this->getOption(self::CAPTION);
    }
    
    protected function _initOptions()
    {
        parent::_initOptions();
        
    	$this->_options = array_merge($this->_options, self::$_initTreeOptions);
    }
    
	/* (non-PHPdoc)
	 * @see Orbas_List_Interface::onListInit()
	 */
	public function onListInit(Orbas_List $list) 
	{
	    $list->setOption(Orbas_List::LIST_PARTIAL, 'common/tree-list.phtml');
	}
	
	public function listAction()
	{
	    $tree = $this->getHelper('Tree');
	    $tree->setDisplayFieldName($this->getOption(self::TREE_NODE_NAME));
	    
	    $this->view->tree = $tree->render();
	    
	    parent::listAction();
	}

	/* (non-PHPdoc)
	 * @see Orbas_List_Interface::onListSearch()
	 */
	public function onListSearch(Zend_Db_Select $select, Orbas_List $list) 
	{
	    $parent = $this->getParam('parent', 0);
	    $select->where('PARENT_SN = ?', $parent);
	}
	
	public function appendAction()
	{
	    $this->_treeAppend();
	    
	    parent::appendAction();
	}
	
	/**
	 * 樹狀append 設定的預設值
	 * 
	 */
	protected function _treeAppend()
	{
	    $parent = $this->getParam('parent', 0);
	     
	    # 父節點
	    $this->_addDefaultAppendData('PARENT_SN', $parent);
	    	  
	    # 路徑
	    $this->_addDefaultAppendData('PATH', $this->_createTreePath($parent));
	}
	
	/**
	 * 刪除資料，並回傳刪除筆數
	 *
	 * @param array $sns
	 */
	protected function _remove($sns)
	{
		$where[] = 'SN IN (' . implode(',', $sns) . ')';
        foreach($sns as $sn) {
            $where[] = 'PATH LIKE "%/' . $sn . '/%"';
        }
        
		return $this->getModel()->delete(implode(' OR ', $where));
	}
	
	/**
	 * 建立樹狀路徑
	 * 
	 * @param integer $parent
	 */
	protected function _createTreePath($parent)
	{
	    $path = '/';
	    
	    if($parent != 0) {
	        $row = $this->getModel()->fetchRowByPrimary($parent);
	        if($row == null) {
	            throw new Orbas_Application_Exception('Param parent value "' . $parent . '" is invalid');
	        }
	        
	        $path = $row['PATH'] . $parent . '/';
	    }
	    
	    return $path;
	}
}
?>