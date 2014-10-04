<?php
/**
 * 提供產生樹狀圖
 * 
 * @author Flash
 *
 */
class Orbas_Tree extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 產生樹狀圖時，所顯示的文字欄位
     * 
     * @var string
     */
    protected $_displayFieldName;
    
	/**
	 * @return the $_displayFieldName
	 */
	public function getDisplayFieldName() 
	{
		return $this->_displayFieldName;
	}

	/**
	 * @param string $_displayFieldName
	 */
	public function setDisplayFieldName($_displayFieldName) 
	{
		$this->_displayFieldName = $_displayFieldName;
		return $this;
	}
	
	/**
	 * 
	 * @var string
	 */
	protected $_modelName;
	
	public function getModelName()
	{
	    if(!$this->_modelName) {
	        $this->_modelName = $this->getActionController()->getModelName();
	    }
	    
	    return $this->_modelName;
	}
	
	public function setModelName($modelName)
	{
	    $this->_modelName = $modelName;
	    return $this;
	}
	
	/**
	 * 
	 * @var Zend_Db_Select
	 */
	protected $_select;
	
	public function setSelect(Zend_Db_Select $select)
	{
	    $this->_select = $select;
	    return $this;
	}
	
	public function getSelect()
	{
	    if(!$this->_select){
	        $model  = Orbas_Model_Broker::get($this->getModelName());
	        $select = $model->select();

	        # 設定排列順序
	        $this->_setOrder($select);
	        
	        /*
	         * 使用者自訂條件
	         */
	        $controller = $this->getActionController();
	        if($controller instanceof Orbas_Tree_Interface) {
	        	$controller->onTreeSearch($select, $this);
	        }
	        
	        $this->_select = $select;
	    }
	    
	    return $this->_select;
	}
	
	/**
     * 排序順序
     * 
     * @var array
     */
    protected $_orderSeq = array(
        'SEQ' => 'asc',
    	'UPDATE_TIME' => 'desc',
        'SN' => 'desc'
    );
    
    /**
     * 設定排列順序
     * 
     * @param Zend_Db_Select $select
     */
    protected function _setOrder(Zend_Db_Select $select)
    {
        $cols = $this->getModel()->info(Zend_Db_Table::COLS);
        foreach($this->_orderSeq as $field => $seq) {
            if(in_array($field, $cols)){
                $select->order($field . ' ' . $seq);
                return;
            }
        }
    }
	
	/**
	 *
	 * @return Orbas_Model_Abstract
	 */
	public function getModel($name = null)
	{
		if($name === null){
			$name = $this->getModelName();
		}
		 
		return Orbas_Model_Broker::get($name);
	}
	
	
	public function render()
	{
	    $this->_url = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
	    
    	return $this->_createTree($this->createStructure());
	}
	
	/**
	 * 
	 * @var Zend_Controller_Action_Helper_Url
	 */
	protected $_url;

    /**
     * 產生dynatree 架構
     * 
     */
    protected function _createTree(ArrayObject $tree, $html = '')
    {
        $html .= "<ul>\n";
        foreach($tree as $treeData) {

            $url = $this->_url->url(array('parent' => $treeData['SN']));
            
            $html .= "<li>\n<a href=\"$url\">";
            $html .= $this->_getNodeName($treeData['NAME']);
            $html .= "</a>\n";
            
            if(isset($treeData['subitems'])){
                $html .= $this->_createTree($treeData['subitems']);
            }
            
            $html .= "</li>\n";
        }
        $html .= "</ul>\n";
        
        return $html;
    }
    
    /**
     * 產生樹狀陣列
     * 
     * @return ArrayObject
     */
    public function createStructure($data = null)
    {
        if($data === null) {
            $data = $this->getData();
        }
        
        $structure = new Orbas_Tree_Structure($data);
        return $structure->create();
    }
    
    /**
     * Tree Data
     * 
     * @var array
     */
    protected $_data;

    /**
     * Get Tree Data
     * 
     * @return array
     */
    public function getData()
    {
        if(!$this->_data) {
            
            $select = $this->getSelect();
            
            $this->_data = $select->query()->fetchAll();
        }
        
        return $this->_data;
    }
    
    /**
     * 產生節點使用的物件
     * 
     * @var Orbas_List_DataAdapter
     */
    protected $_nameAdpater = false;
    
    protected function _getNodeName($name)
    {
        if($this->_nameAdpater === false) {
            $list   = Zend_Controller_Action_HelperBroker::getStaticHelper('List');
            $config = Orbas_Config_Broker::get($this->getModelName())->fields;
            $field  = $this->getDisplayFieldName();

            if(!isset($config->$field)) {
                throw new Orbas_Application_Exception("Can't get the '$field' in the config");
            }
            
            $this->_nameAdpater = $list->getDataAdapter($config->$field);
        } 

        return $this->_nameAdpater->getText($name);
    }
    
    /**
     * 取得樹狀值的路徑
     * 
     * @param integer $value
     * @param string  $modelName
     */
    static public function getPathByValue($value, $modelName)
    {
        $model = Orbas_Model_Broker::get($modelName);
        $row   = $model->fetchRowByPrimary($value);
        
        return $row ? $row['PATH'] : null;
    }
    
    public function __clone()
    {
        $this->_select = null;
        $this->_data = null;
    }
}
?>