<?php
/**
 * 表單過濾器
 * 
 * @author Ivan
 *
 */
class Orbas_Form_Filter
{
    /**
     * 
     * @var array
     */
    protected $_options = array();
    
    /**
     * 
     * @var Orbas_Controller_Action
     */
    protected $_controller;
    
    /**
     * construct
     * 
     * @param Orbas_Controller_Action $controller
     * @param array $options
     */
    public function __construct(Orbas_Controller_Action $controller, $options = array())
    {
        $this->_controller = $controller;
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * 
     * @var Orbas_DataObject
     */
    protected $_data;

    /**
     * 過濾及轉換資料
     * 
     * @param array $data
     */
    public function filter($data)
    {
        if(is_array($data)){
            $this->_data = new Orbas_DataObject($data);
        } else if($data instanceof ArrayObject) {
            $this->_data = new Orbas_DataObject($data->getArrayCopy());
        } else if($data instanceof Orbas_DataObject) {
            $this->_data = $data;
        } else if (is_object($data) && method_exists($data, 'toArray')) {
            $this->_data = new Orbas_DataObject($data->toArray());
        } else {
            throw new Orbas_Application_Exception(__CLASS__ . ' class data type is error');
        }

        $configs = $this->getConfig()->fields;
        
        foreach($configs as $field => $config) {
            $filterClass = $this->_getFilterClass($config->type);
            if($filterClass !== null) {
                $filterClass->filter($field);
            }
        }
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
     * @return Zend_Config
     */
    public function getConfig()
    {
    	if(!$this->_config) {
    		$this->_config = Orbas_Config_Broker::get($this->_controller->getModelName());
    	}
    
    	return $this->_config;
    }
    
    /**
     * 
     * @var array
     */
    protected $_filterElements = array();
    
    /**
     * 取得過濾器類別
     * 
     * @param string $type
     * @return Orbas_Form_Filter_Abstract|null
     */
    public function _getFilterClass($type)
    {
        if(isset($this->_filterElements[$type])){
            return $this->_filterElements[$type];
        }
        
        $path = APPLICATION_PATH . '/../library/Orbas/Form/Filter/' . ucfirst($type) . '.php';
        if(!file_exists($path)){
            return null;
        }

        $className = 'Orbas_Form_Filter_' . ucfirst($type);
        $this->_filterElements[$type] = new $className($this->_data);
        
        return $this->_filterElements[$type];
    }
}
?>