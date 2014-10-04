<?php
/**
 * 後臺管理
 * 
 * @author Ivan
 *
 */
abstract class Orbas_Controller_Workbench extends Orbas_Controller_Action
{
    const CAPTION = 'caption';  # 列表標題
    
	const NAVIGATION = 'navigation';  # 是否自動產生Navigation
	
	const AUTO_INSERT_UPDATE_INFO = 'autoInsertUpdateInfo'; # 自動增加修改資訊(操作人員、最後修改)
	
	const DEFAULT_LOCALE = 'defaultLocale';    # 後台預設使用的語系
	
	const ENABLE_TRANSACTION = 'enableTransaction'; # 是否開啟db transaction
	const ENABLE_APPEND      = 'enableAppend';      # 是否開啟新增功能
	const ENABLE_DELETE      = 'enableDelete';      # 是否開啟刪除功能
	const ENABLE_SEQUENCE    = 'enableSequence';    # 是否啟用排序功能
	const ENABLE_LIST_SELECT = 'enableListSelect';  # 是否啟用列表選擇功能
	const ENABLE_CUSTOM_APPEND_URL = 'enableCustomoAppendUrl'; # 自訂新增連結網址
	const ENABLE_LIST_TOOLBAR = 'enableListToolbar'; # 是否啟用列表功能列

	static protected $_initOptions = array(
	    self::CAPTION    => '',
		self::NAVIGATION => true,
	    self::AUTO_INSERT_UPDATE_INFO => true,
	    self::ENABLE_TRANSACTION => false,
	    self::ENABLE_LIST_TOOLBAR => true,
	    self::ENABLE_APPEND      => true,
	    self::ENABLE_DELETE      => true,
	    self::ENABLE_SEQUENCE    => false,
	    self::ENABLE_LIST_SELECT => true,
	    self::ENABLE_CUSTOM_APPEND_URL => false,
	    self::DEFAULT_LOCALE => 'zh_TW'
	);
	
	/**
	 * 
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * 
	 * @param string $name
	 * @return multitype:|NULL
	 */
	public function getOption($name)
	{
		if(isset($this->_options[$name])){
			return $this->_options[$name];
		}
		
		return null;
	}
	
	public function setOption($name, $value)
	{
	    $this->_options[$name] = $value;
	    return $this;
	}
	
	public function init()
	{
		parent::init();
		
		$this->_initOptions();
		
		if($this->getOption(self::NAVIGATION)){
			$this->_initNavigation();
		}
		
		if($this->getOption(self::DEFAULT_LOCALE)){ 
		    Orbas_Translate::setLocale($this->getOption(self::DEFAULT_LOCALE));
		}
	}
	
	protected function _initOptions()
	{
		$this->_options = self::$_initOptions;
	}
	
	public function indexAction()
	{
		$this->_helper->redirector('list');
	}
	
	/**
	 * 
	 * @var Zend_Navigation
	 */
	protected $_navigation;
	
	/**
	 * 初始化設定Navigation
	 * 
	 */
	protected function _initNavigation()
	{
		$configPath = APPLICATION_PATH . '/configs/navigation/' . $this->getRequest()->getModuleName() . '.php';
		$this->_navigation = new Zend_Navigation(require $configPath);
		
		$this->view->navigation($this->_navigation);
	}
	
	/**
	 * 列表頁
	 * 
	 */
	public function listAction()
	{
	    $this->getMessageFromSession();
	    
	    # 設定頁面控制選項
	    $this->_assignEnableOptions();

	    # 集合提供器
	    $this->view->enumProvider = $this->getHelper('EnumProvider');
	    
	    /*
	     * 自訂toolbar
	     */
	    $viewScripts = $this->view->getScriptPaths();
	    $noController = $this->_helper->viewRenderer->getNoController();
	    $controllerName = str_replace('.', '-', $this->getRequest()->getControllerName());
	    foreach($viewScripts as $script) {
	        if(!$noController) {
	            $script = rtrim($script, '/') . '/' . $controllerName . '/toolbar.phtml';
	        }
	        
	        if(file_exists($script)) {
	            $this->view->customToolbar = $noController ? 'toolbar.phtml' : $controllerName . '/toolbar.phtml';
	            break;
	        }
	    }
	    
		$this->getHelper('List')->render();
	}
	
	/**
	 * 設定頁面控制選項
	 * 
	 */
	protected function _assignEnableOptions()
	{
	    $view = $this->view;
	    foreach($this->_options as $key => $option) {
	        if(strpos($key, 'enable') === 0) {
	            $view->assign($key, $option);
	        }
	    }
	}

	/**
	 * 編輯頁
	 * 
	 */
	public function editAction()
	{
	    if(!$this->hasParam('sn')){
	        exit;
	    }
	    
	    $this->getMessageFromSession();
	    
	    $row = $this->getModel()->fetchRowByPrimary($this->getParam('sn'));
	    if(!$row){
	        throw new Orbas_Application_Exception('查無資料');
	    }
	    
	    # 編輯的資料
	    $data = new Orbas_DataObject($row);
	    $this->view->data = $data;
	    
	    # 集合提供器
	    $this->view->enumProvider = $this->getHelper('EnumProvider');
	    
	    /*
	     * 新增麵包屑
	     */ 
	    $page = new Zend_Navigation_Page_Uri(array(
            'visible' => false,
	        'label'   => $this->_getEditBreadcrumb($this->view->data),
	        'active'  => true,
	    	'uri' => $this->_helper->url->url()
	    ));
	    $this->_addBreadcrumb($page);
	    
	    $this->_beforeEditFormRenderEvent($data);
	    
		$this->render('form', null, $this->_helper->viewRenderer->getNoController());
	}
	
	/**
	 * Render Edit Form 前的事件
	 * 
	 * @param Orbas_DataObject $data
	 */
	protected function _beforeEditFormRenderEvent(Orbas_DataObject $data)
	{
	    // do nothing
	}
	
	/**
	 * 取得編輯時的麵包屑
	 * 
	 * @param Orbas_DataObject $row
	 */
	abstract protected function _getEditBreadcrumb(Orbas_DataObject $row);
	
	/**
	 * 新增麵包屑，於該層級再往下加一層
	 * 
	 * @param Zend_Navigation_Page $page
	 * @param string $parentId  指定parent的ID，如果沒有則用Controller搜尋parent
	 */
	protected function _addBreadcrumb(Zend_Navigation_Page $page, $parentId = null)
	{
	    if($parentId !== null) {
	        $parent = $this->_navigation->findById($parentId);
	    } else {
	        $parent = $this->_navigation->findByController($this->getRequest()->getControllerName());
	    }
	    
	    if($parent) {
	        $parent->addPage($page);
	    }
	}
	
	/**
	 * 
	 * @var array
	 */
	protected $_postData = false;
	
	/**
	 * @return the $_postData
	 */
	public function getPostData() 
	{
	    if($this->_postData === false){
	        $this->_postData = $this->getRequest()->getPost();
	    }
	    
		return $this->_postData;
	}

	/**
	 * @param multitype: $_postData
	 */
	public function setPostData($_postData) 
	{
		$this->_postData = $_postData;
		return $this;
	}

	/**
	 * 執行修改
	 * 
	 */
	public function updateAction()
	{
	    if($this->getOption(self::ENABLE_TRANSACTION)){
	        $db = Zend_Db_Table::getDefaultAdapter();
	        $db->beginTransaction();
	    }
	    
	    try {
	        $data = new Orbas_DataObject($this->getPostData());
	        
	        /*
	         * 過濾欄位
	         */
	        $filter = new Orbas_Form_Filter($this);
	        $filter->filter($data);
	        
	        /*
	         * 驗證資料
	         */
	        $validator = $this->getFormValidator($data);
	        if(!$validator->isValid($data->toArray())){
	            throw new Orbas_Form_Validator_Exception($validator->getMessages());
	        }
	        
	        if($this->getOption(self::AUTO_INSERT_UPDATE_INFO)) {
	            $user = $this->getHelper('Auth')->getIdentity();
	            $data->set('UPDATE_USER_SN', $user['SN']);
	            $data->set('UPDATE_TIME',    date('Y-m-d H:i:s'));
	        }
	        
	        # 開始更新前的事件
	        $this->_preUpdate($data);
	        
	        # 轉換Post，將陣列值改為序列化
	        $this->_transformPostData($data);
	        
	        # 更新資料
	        $this->getModel()
	             ->updateByPrimary($data);
	             
	        # 更新後的事件
	        $this->_postUpdate($data);

            $this->_setSuccessMessage('儲存成功');

            if($this->getOption(self::ENABLE_TRANSACTION)){
                $db->commit();
            }
            
            $this->_helper->json(array(
                'error' => 0
            ));
	        
	    } catch (Orbas_Form_Validator_Exception $e) {
	        
	        if($this->getOption(self::ENABLE_TRANSACTION)){
	            $db->rollBack();
	        }
	        
            $this->_helper->json(array(
	           'error'   => 1,
	           'fields'  => $e->getMessages()  
	        ));
	        
	    } catch (Exception $e) {
	        
	       if($this->getOption(self::ENABLE_TRANSACTION)){
	            $db->rollBack();
	        }
	        
	        $this->_helper->json(array(
	        	'error'   => 1,
                'message' => $e->getMessage()
	        ));
	    }
	}

	/**
	 * 修改前的事件
	 * 
	 */
	protected function _preUpdate(Orbas_DataObject $data)
	{
	    // do nothing
	}
	
	/**
	 * 修改後的事件
	 * 
	 */
	protected function _postUpdate(Orbas_DataObject $data)
	{
	   // do nothing 
	}
	
	/**
	 * 序列化存入資料表內連結符號
	 * 
	 * @var string
	 */
	static public $_serailizeGlue = ',';
	
	/**
	 * 轉換data內的值為序列化，方便輸入資料表內
	 * 
	 * @param Orbas_DataObject $data
	 * @return array
	 */
	protected function _transformPostData(Orbas_DataObject $data)
	{
	    foreach($data as $key => $value) {
	        if(is_array($value)){
	            $data->$key = implode(self::$_serailizeGlue, $value);
	        }
	    }
	}

	/**
	 * 
	 * 驗證表單的工具
	 * 
	 * @var Zend_Form
	 */
	protected $_formValidator = null;

	/**
	 * 驗證表單物件參數
	 * 
	 * @var array
	 */
	protected $_formValidatorOption = array();
	
	/**
	 * 
	 * @param Orbas_DataObject
	 * @return Zend_Form
	 */
	public function getFormValidator(Orbas_DataObject $data)
	{
	    if($this->_formValidator === null) {
	       $validator = new Orbas_Form_Validator($this, $this->_formValidatorOption);
	       $this->_formValidator = $validator->create($data);
	    }
	    
	    return $this->_formValidator;
	}
	
	/**
	 * 新增表單的預設值
	 * 
	 * @var array
	 */
	protected $_defaultAppendData = array();
	
	/**
	 * 增加新增表單的預設值
	 * 
	 * @param mixed int|string $key
	 * @param mixed int|string $value
	 */
	protected function _addDefaultAppendData($key, $value)
	{
	    $this->_defaultAppendData[$key] = $value;
	    return $this;
	}
	
	/**
	 * 新增頁
	 * 
	 */
	public function appendAction()
	{
	    if(!$this->getOption(self::ENABLE_APPEND)){
	    	throw new Exception('不允許新增資料');
	    }
	    
	    $this->getMessageFromSession();
	    
	    # 編輯的資料
	    $data = new Orbas_DataObject($this->_defaultAppendData);
	    $this->view->data = $data;
	    
	    # 集合提供器
	    $this->view->enumProvider = $this->getHelper('EnumProvider');
	    
	    /*
	     * 新增麵包屑
	     */ 
	    $appendBreadCrumb = '新增' . $this->getOption(self::CAPTION);
	    $page = new Zend_Navigation_Page_Uri(array(
            'visible' => false,
	        'active'  => true,
	        'label'   => $appendBreadCrumb,
	    	'uri' => $this->_helper->url->url()
	    ));
	    $this->_addBreadcrumb($page);
	    
	    $this->_beforeAppendFormRenderEvent($data);
	    
		$this->render('form', null, $this->_helper->viewRenderer->getNoController());
	}
	
	/**
	 * Append Form Render 前的事件
	 * 
	 * @param Orbas_DataObject $data
	 */
	protected function _beforeAppendFormRenderEvent(Orbas_DataObject $data)
	{
	    // do nothing
	}
	
	/**
	 * 執行新增
	 * 
	 */
	public function appendDoAction()
	{
	    if($this->getOption(self::ENABLE_TRANSACTION)){
	    	$db = Zend_Db_Table::getDefaultAdapter();
	    	$db->beginTransaction();
	    }
	    
	    try {
	        
	        if(!$this->getOption(self::ENABLE_APPEND)){
	        	 throw new Exception('不允許新增資料');
	        }
	    	 
    		$data = new Orbas_DataObject($this->getPostData());
    		
    		/*
    		 * 過濾欄位
    		 */
    		$filter = new Orbas_Form_Filter($this);
    		$filter->filter($data);

    		/*
    		 * 驗證資料
    		 */
    		$validator = $this->getFormValidator($data);
    		if(!$validator->isValid($data->toArray())){
                throw new Orbas_Form_Validator_Exception($validator->getMessages());
    		}
    		
    		if($this->getOption(self::AUTO_INSERT_UPDATE_INFO)) {
    		    $user = $this->getHelper('Auth')->getIdentity();
    		    $data->set('UPDATE_USER_SN', $user['SN']);
    		    $data->set('UPDATE_TIME',    date('Y-m-d H:i:s'));
    		}
    		
    		# 新增前的事件
    		$this->_preAppend($data);
    		
    		# 轉換Post，將陣列值改為序列化
    		$this->_transformPostData($data);
    		
    		# 儲存資料
    		$model = $this->getModel();
    		$sn = $model->append($data);
    		           
            # 增加SEQ排序
            $cols = $model->info(Zend_Db_Table::COLS);
            if(in_array('SEQ', $cols)) {
                $model->update(array('SEQ' => $sn), 'SN = ' . $sn);
                $data['SEQ'] = $sn;
            }
    		           
            # 新增後的事件
            $data['SN'] = $sn;
            $this->_postAppend($data);
    
            $this->_setSuccessMessage('新增成功');
            
            if($this->getOption(self::ENABLE_TRANSACTION)){
            	$db->commit();
            }
    		           
    		$this->_helper->json(array(
        		'error'   => 0,
    		    'sn'      => $sn
            ));
	    						 
	    } catch (Orbas_Form_Validator_Exception $e) {
	     
	        if($this->getOption(self::ENABLE_TRANSACTION)){
	        	$db->rollBack();
	        }
	        
    	    $this->_helper->json(array(
	    		'error'   => 1,
	    		'fields'  => $e->getMessages()
    	    ));
	     
	    } catch (Exception $e) {
	     
	        if($this->getOption(self::ENABLE_TRANSACTION)){
	        	$db->rollBack();
	        }
	        
    	    $this->_helper->json(array(
    	    	'error'   => 1,
    	    	'message' => $e->getMessage()
    	   	));
        }
	}
	
	/**
	 * 新增前的事件
	 * 
	 */
	protected function _preAppend(Orbas_DataObject $data)
	{
	    // do nothing
	}
	
	/**
	 * 新增後的事件
	 * 
	 * @param Orbas_DataObject $data
	 */
	protected function _postAppend(Orbas_DataObject $data)
	{
	    // do nothing
	}
	
	/**
	 * 移除資料
	 * 
	 */
	public function removeAction()
	{
	    try{
	        
	        if(!$this->getOption(self::ENABLE_DELETE)){
	            throw new Exception('不允許刪除資料');
	        }
	        
	        $sns = $this->getRequest()->getPost('sn');
	        
	        if(empty($sns)){
	            throw new Exception('未勾選要刪除的資料');
	        }
	        
	        $number = $this->_remove($sns);
	        
	        $this->_setSuccessMessage(sprintf('已刪除 %d 筆資料', $number));
	        
	        $this->_helper->json(array(
	        	'error'   => 0
	        ));
	        
	    } catch (Exception $e) {
	        $this->_helper->json(array(
	        	'error'    => 1,
	            'message'  => $e->getMessage()
	        ));
	    }
	}
	
	/**
	 * 刪除資料，並回傳刪除筆數
	 * 
	 * @param array $sns
	 */
	protected function _remove($sns)
	{
	    return $this->getModel()
	                ->delete('SN IN (' . implode(',', $sns) . ')');
	}
	
	/**
	 * 
	 * 儲存成功訊息至session
	 * 
	 * @param string $message
	 * @return Orbas_Controller_Workbench
	 */
	protected function _setSuccessMessage($message) 
	{
	    $this->sessionSet('message', array(
            'type' => MESSAGE_SUCCESS,
            'msg'  => $message
	    ));
	    
	    return $this;
	}
	
	/**
	 * 資料排序
	 * 
	 */
	public function moveAction()
	{
	    try {
	        
	        if(!$this->getOption(self::ENABLE_SEQUENCE)) {
	            exit;
	        }

	        $model  = $this->getModel();
	        
	        # 依照List排列方式取得位置
	        $select = $this->getHelper('List')->getSelect();
	        
	        # 取得欲插入排序的原位置資料 (2)
	        $seq = $this->getParam('seq');
	        $select->limit(1, $seq-1);
	        $insertedRow = $model->fetchrow($select);
	        
	        if($insertedRow === null) {
	            throw new Exception('排序位置錯誤');
	        }
	        
	        # 取得原本的資料 (1)
	        $sn = $this->getParam('sn');
	        $orgRow = $model->fetchRowByPrimary($sn);
	        
	        $tableName = $model->info(Zend_Db_Table::NAME);
	        $db = $model->getAdapter();
	         
	        /*
	         * 先檢查(1)的順序與(2)的順序關係
	         * 1. (1) > (2)： 大於等於(2) 小於 (1) 的資料SEQ通通+1
	         * 2. (1) < (2)： 大於(1) 小於等於 (2) 的資料SEQ通通-1
	         */
	        if($orgRow['SEQ'] > $insertedRow['SEQ']) {
	           $sql  = "UPDATE $tableName SET SEQ = SEQ+1 ";
	           $sql .= 'WHERE SEQ >= ' . $insertedRow['SEQ'] . ' AND SEQ < ' . $orgRow['SEQ'];
	           $db->query($sql);
	        } else {
	           $sql  = "UPDATE $tableName SET SEQ = SEQ-1 ";
	           $sql .= 'WHERE SEQ > ' . $orgRow['SEQ'] . ' AND SEQ <= ' . $insertedRow['SEQ'];
	           $db->query($sql);
	        }
	        
	        # (1) 的順序改為(2)的SEQ
	        $orgRow->SEQ = $insertedRow['SEQ'];
	        $orgRow->save();
	        
	        $this->_setSuccessMessage('排序完成');
	        $this->_helper->json(array(
	        	'error' => 0
	        ));
	        
	    } catch (Exception $e) {
	        $this->_helper->json(array(
                'error' => 1,
                'message' => $e->getMessage()
	        ));
	    }
	}
}
?>