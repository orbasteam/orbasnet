<?php
/**
 * 產生List所需的方法
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_List extends Zend_Controller_Action_Helper_Abstract
{
    const PAGINATION	= 'pagination';    # 是否啟用換頁
    const ROW_COUNT		= 'rowCount';      # 每頁筆數
    
    const LIST_PARTIAL 	= 'listPartial';   # 產生List的view
    const LIST_GROUP    = 'listGroup';     # 列表順序設定群組
    
    const PAGINATOR_PARTIAL       = 'paginatorPartial'; # 產生換頁控制器的view
    const PAGINATOR_ADAPTER_CLASS_NAME = 'paginatorAdapterClassName';
    
    protected $_options = array(
        self::PAGINATION    => true,
        self::ROW_COUNT     => 25,
    	self::LIST_PARTIAL	=> 'common/list.phtml',
        self::LIST_GROUP    => 'default',
        self::PAGINATOR_PARTIAL => 'common/paginator/default.phtml',
        self::PAGINATOR_ADAPTER_CLASS_NAME => 'Zend_Paginator_Adapter_DbSelect' 
    );
    
    public function init()
    {
        $controller = $this->getActionController();
        if($controller instanceof Orbas_List_Interface){
            $controller->onListInit($this);
        }
    }
    
    /**
     * @return the $_options
     */
    public function getOption($key) 
    {
        if(isset($this->_options[$key])){
            return $this->_options[$key];
        }
        
    	return null;
    }
    
    /**
     * @param multitype:boolean number  $_options
     */
    public function setOption($key, $value) 
    {
    	$this->_options[$key] = $value;
    	return $this;
    }
    
    /**
     * 
     * @var string
     */
    protected $_modelName;
    
	/**
	 * @return the $_modelName
	 */
	public function getModelName() 
	{
	    if(!$this->_modelName) {
	        $this->_modelName = $this->getActionController()->getModelName();
	    }
	    
		return $this->_modelName;
	}

	/**
	 * @param string $_modelName
	 */
	public function setModelName($_modelName) 
	{
		$this->_modelName = $_modelName;
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
	
	/**
	 * 
	 * @var Zend_Paginator|Zend_Db_Table_Rowset_Abstract
	 */
	protected $_data = false;
	
	/**
	 * 
	 * @var Zend_Db_Select
	 */
	protected $_select;
	
	/**
	 * 
	 * @return Zend_Db_Select
	 */
	public function getSelect()
	{
	    if(!$this->_select){
	        $select = $this->getModel()->select();
	        
	        # 設定排列順序
	        if($this->getRequest()->has('order')){
	           $field = $this->getRequest()->getParam('order');
	           $desc  = $this->getRequest()->getParam('desc') == 1 ? 'desc' : 'asc';
	           $select->order($field . ' ' . $desc);
	        } else {
	            $this->_setOrder($select);
	        }
	        
	        /*
	         * 自訂過濾條件
	        */
	        $controller = $this->getActionController();
	        if($controller instanceof Orbas_List_Interface) {
	        	$controller->onListSearch($select, $this);
	        }
	        
	        $this->_select = $select;
	    }
	    
	    return $this->_select;
	}
	
	/**
	 * 
	 */
	public function setSelect(Zend_Db_Select $select)
	{
	    $this->_select = $select;
	    return $this;
	}
	
	/**
	 * 取得List資料
	 * 
	 * @return Zend_Paginator|Zend_Db_Table_Rowset_Abstract
	 */
	public function getData()
    {
        if($this->_data === false){
            
            $select = $this->getSelect();

            if($this->getOption(self::PAGINATION)){
            	 
            	Zend_View_Helper_PaginationControl::setDefaultViewPartial($this->getOption(self::PAGINATOR_PARTIAL));
            
            	$adapterName = $this->getOption(self::PAGINATOR_ADAPTER_CLASS_NAME);
            	$paginator = new Zend_Paginator(new $adapterName($select));
            	$paginator->setDefaultItemCountPerPage($this->getOption(self::ROW_COUNT));
            	$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', 1));
            	$paginator->setView($this->getActionController()->view);
            
            	$this->_data = $paginator;
            	
            } else {
                
                $this->_data = $this->getModel($this->getModelName())
                                    ->fetchAll($select);
            }
        }
        
        return $this->_data;
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
     * 產生列表
     * 
     */
    public function render()
    {
        $controller = $this->getActionController();
        $view = $controller->view;
        $view->list = $this;

        /*
         * 先抓取Controller底下有沒有list.phtml，沒有的話再從共同資料夾取得
         */
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $scriptName   = $viewRenderer->getViewScript('list');
        
        $moduleName = $this->getRequest()->getModuleName();
        $scriptPath = APPLICATION_PATH . '/views/scripts/' . $moduleName . '/' . $scriptName;
        if(file_exists($scriptPath)) {
            $controller->renderScript($scriptName);
        } else {
            $controller->renderScript($this->getOption(self::LIST_PARTIAL));
        }
        
    }
    
    public function getTitle()
    {
        return $this->getTitleFromConfig();
    }
    
    /**
     * 取得列表header
     * 
     * @return array
     */
    public function getTitleFromConfig()
    {
        $title  = array();
        $config = $this->getConfig();
        
        foreach($this->getListConfig() as $key => $field) {
            
            if($field instanceof Zend_Config){
                
                if(is_integer($key)) {
                    
                    /*
                     * 自訂欄位
                     */
                    foreach($field as $customField => $customConfig) {
                    	$title[$customField] = $customConfig->name;
                    }
                    
                /*
                 * 自訂型態
                 */
                } else {
                    
                    if(isset($field->name)) { 
                        $title[$key] = $field->name;
                    } else {
                        
                        $fieldConfig = $this->getFieldConfig($key);
                        $title[$key] = $fieldConfig->name;
                    }
                }
                
            } else {
                
                $fieldConfig = $this->getFieldConfig($field);
                $title[$field] = $fieldConfig->name;
            }
        }
        
        return $title;
    }
    
    /**
     * 取得換頁物件
     * 
     * @return Zend_Paginator
     */
    public function getPaginator()
    {
        if(!$this->getOption(self::PAGINATION)){
            throw new Orbas_Application_Exception('List paginator is disabled');
        }
        
        return $this->getData();
    }
    

    /**
     * 已轉換數值過的資料
     * 
     * @var array
     */
    protected $_transformedData = false;
    
    /**
     * 取得已轉換的資料
     * 
     */
    public function getTransformedData()
    {
        if($this->_transformedData === false) {
            
            $this->_transformedData = array();
            
            $dataAdapter = $this->_getDataAdapters();
            foreach($this->getData() as $data) {
                foreach($data as $field => $value) {
                    if(isset($dataAdapter[$field]) && $value !== null){
                    	$class = $dataAdapter[$field];
                    	$class->addForeignValue($value);
                    }
                }
            }
            
            foreach($this->getData() as $data) {
                foreach($this->getTitleFromConfig() as $field => $name) {
                    if(isset($dataAdapter[$field])){
                    	$class = $dataAdapter[$field];
                    	$value = $class->getText($data[$field]);
                    } else {
                        $value = $data[$field];
                    }
                    
                    $dataValue[$field] = $value;
                }
                
                $this->_transformedData[$data['SN']] = $dataValue;
            }
        }
        
        return $this->_transformedData;
    }
    
    /**
     * 資料轉換物件
     * 
     * @var array
     */
    protected $_dataAdapters = array();
    
    /**
     * 取得資料轉換提供物件
     * 
     * @return array
     */
    protected function _getDataAdapters()
    {
        if(!$this->_dataAdapters){
            
            foreach($this->getListConfig() as $key => $field) {
           	    if($field instanceof Zend_Config){
            
               		/*
               		 * 自訂欄位
                	 */
               		if(is_integer($key)) {
               			foreach($field as $customField => $customConfig) {
               				$adapter = $this->getDataAdapter($customConfig);
               				if($adapter) $this->_dataAdapters[$customField] = $adapter;
               			}
               			
               		/*
               		 * 自訂型態
               		 */
               		} else {
               		    
                		# 取得欄位adapter
                		$adapter = $this->getDataAdapter($field);
                		if($adapter) $this->_dataAdapters[$key] = $adapter;
                	}
                } else {
                
                	# 取得欄位adapter
                    $adapter = $this->getDataAdapter($this->getFieldConfig($field));
                	if($adapter) $this->_dataAdapters[$field] = $adapter;
                }
            }
        }
        return $this->_dataAdapters;
    }
    
    /**
     * 取得資料轉換物件
     * 
     * @param Zend_Config  $config
     * @return Orbas_List_DataAdapter
     */
    public function getDataAdapter(Zend_Config $config)
    {
        $file = realpath(APPLICATION_PATH . '/../library/Orbas/List/DataAdapter/' . ucfirst($config->type) . '.php');
        if(file_exists($file)){
        	$className = 'Orbas_List_DataAdapter_' . ucfirst($config->type);
        	return new $className($this, $config);
        }
        
        return null;
    }
    
    /**
     * 取得列表順序設定檔
     * 
     * @return array
     */
    public function getListConfig()
    {
        $group = $this->getOption(self::LIST_GROUP);
        return $this->getConfig()->list->$group;
    }
    
    /**
     * 取得排序的欄位
     * 
     * @return array|null
     */
    public function getOrderFields()
    {
        $group  = $this->getOption(self::LIST_GROUP);
        $config = $this->getConfig();
        if(isset($config->order) && $config->order->$group) {
            return $config->order->$group->toArray();
        }
        
        return null;
    }
    
    /**
     * 
     * List 設定檔
     * 
     * @var Zend_Config
     */
    protected $_config;
    
	/**
	 * @param Zend_Config $config
	 */
	public function setConfig($config) 
	{
		if(!$config instanceof Zend_Config) {
		    $config = new Zend_Config($config);
		}
	    
		$this->_config = $config;
	}

	/**
     * 取得List的設定檔
     * 放置於application/configs/model
     * 
     */
    public function getConfig()
    {
        if(!$this->_config) {
            $this->_config = Orbas_Config_Broker::get($this->getModelName());
        }
        
        return $this->_config;
    }
    
    /**
     * 取得欄位設定檔
     * 
     * @param string $field
     */
    public function getFieldConfig($field)
    {
        if(isset($this->getConfig()->fields->$field)){
            return $this->getConfig()->fields->$field;
        }
        
        return null;
    }
}
?>