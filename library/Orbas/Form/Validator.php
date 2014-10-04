<?php
/**
 * 
 * 抓取資料表metadata
 * 自動產生表單驗證物件
 * 
 * @author Ivan
 *
 */
class Orbas_Form_Validator
{
    /*
     * 資料型態驗證時，如果欄位在postData為null
     * 驗證時常會有問題，因此建議關閉驗證null的資料型態
     * 以防錯誤發生
     */
    const VALIDATE_NULL_DATA = 'validateNullPostData';
    
    /*
     * 驗證群組，沒有在驗證群組內的欄位不做驗證
     */
    const VALIDATE_GROUP = 'validateGroup';
    
    protected $_options = array(
    	self::VALIDATE_NULL_DATA => false,
        self::VALIDATE_GROUP => null
    );
    
    /**
     * 
     * 取得驗證參數
     * 
     * @param string $name
     * @throws Orbas_Application_Exception
     * @return mixed
     */
    public function getOption($name)
    {
        if(isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        
        return null;
    }
    
    /**
     * 
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        return $this;
    }
    
    /**
     * 
     * @var Orbas_Controller_Action
     */
    protected $_controller;
    
    /**
     * 
     * @param Orbas_Controller_Action $controller
     */
    public function __construct(Orbas_Controller_Action $controller, $options = array())
    {
        $this->_controller = $controller;
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * 
     * @var string
     */
    protected $_modelName;
    
    /**
     * 
     * @return string
     */
    public function getModelName()
    {
        if(!$this->_modelName) {
            $this->_modelName = $this->_controller->getModelName();
        }
        
        return $this->_modelName;
    }
    
    /**
     * 
     * @param string $name
     */
    public function setModelName($name)
    {
        $this->_modelName = $name;
        return $this;
    }
    
    /**
     * 
     * @param array $postData
     * @return Zend_Form
     */
    public function create($postData = array())
    {
        $form  = new Zend_Form();
        $model = Orbas_Model_Broker::get($this->getModelName());
        $metaData = $model->info(Zend_Db_Table::METADATA);

        # 驗證群組
        $validateGroup = $this->getValidateConfig();
        
        foreach($metaData as $field => $data) {
            
            # 主鍵不做檢查
            if($data['PRIMARY'] === true) {
                continue;
            }
            
            /*
             * 不在驗證群組內的值,不做驗證
             */
            if($validateGroup !== null && !in_array($field, $validateGroup->toArray())){
                continue;
            }
            
            $element = self::getElement($field);
            
            # 檢查是否允許empty
            if($data['NULLABLE'] === false && $data['DEFAULT'] === null) {
                $element->setAllowEmpty(false)
                        ->addValidator(new Zend_Validate_NotEmpty());
            }
            
            if( (isset($postData[$field]) && $postData[$field] != '') || 
                (!isset($postData[$field]) && $this->getOption(self::VALIDATE_NULL_DATA))){
                
                # 長度限制
                if(!empty($data['LENGTH'])){
                	$element->addValidator(new Zend_Validate_StringLength(
                			array('max' => $data['LENGTH'])
                	));
                }
                
                # 限制整數
                if(strpos($data['DATA_TYPE'], 'int') !== false){
                    $element->addValidator(new Zend_Validate_Int());
                }
                
            	# 檢查日期格式
            	if($data['DATA_TYPE'] == 'date') {
                	$element->addValidator(new Zend_Validate_Date());
                }
                
                # 驗證 EMAIL
                if(strpos($field, 'EMAIL') !== false) {
                	$element->addValidator(new Zend_Validate_EmailAddress());
                }
            }
            
            $form->addElement($element);
        }
        
        # 使用者自訂驗證
        if($this->_controller instanceof Orbas_Form_Validator_Interface) {
            $this->_controller->setupCustomFormValidator($form, $this);
        }
        
        return $form;
    }

    /**
     * 取得Zend_Form元素
     *  
     * @param string $name
     * 
     * @return Zend_Form_Element
     */
    static public function getElement($name)
    {
        return new Zend_Form_Element($name);
    } 
    
    /**
     * 取得群組驗證設定檔
     * 
     * @param string $group
     * @throws Orbas_Application_Exception
     * @return Zend_Config
     */
    public function getValidateConfig()
    {
        $group = $this->getOption(self::VALIDATE_GROUP);
        if($group === null) {
        	return null;
        }
        
        $config = $this->getConfig();
        
        if(!isset($config->validate->$group)) {
        	throw new Orbas_Application_Exception('No validate Group "' . $group . '" in the config');
        }
        
        return $config->validate->$group;
    }
    
    /**
     * 
     * 資料設定檔
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
     * 取得資料設定檔
     * 
     */
    public function getConfig()
    {
        if(!$this->_config) {
            $this->_config = Orbas_Config_Broker::get($this->getModelName());
        }
        
        return $this->_config;
    }
}
?>