<?php
/**
 * 處理上傳圖片，並縮放大小
 * 
 * @author Ivan
 *
 */
class Orbas_Image
{
    /**
     * 縮放寬度
     * 
     * @var integer
     */
    protected $_width;
    
    /**
     * 縮放高度
     * 
     * @var integer
     */
    protected $_height;

    /**
     * 保持縮放比例
     * 
     * @var boolean
     */
    protected $_keepRatio = true;
    
    /**
     * 上傳目的地
     * 
     * @var string
     */
    protected $_destination;
    
    /**
     * 是否更新至資料表
     *
     * @var boolean
     */
    protected $_updateDb = true;
    
    /**
     * 檔案名稱(不包含副檔名)
     * 
     * @var string
     */
    protected $_fileName;
    
    /**
     * 保持原檔名
     * 
     * @var boolean
     */
    protected $_keepFileName = false;
    
    /**
     * 資料表主鍵 (更新至資料庫時使用)
     * 
     * @var integer
     */
    protected $_primaryKey;
    
    /**
     * Model名稱
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
	        $this->_modelName = $this->_controller->getModelName();
	    }
	    
		return $this->_modelName;
	}

	/**
	 * @param string $_modelName
	 */
	public function setModelName($_modelName) 
	{
		$this->_modelName = $_modelName;
		return $this;
	}
	
	/**
	 * 
	 * @return Orbas_Model_Abstract
	 */
	public function getModel()
	{
	    return Orbas_Model_Broker::get($this->getModelName());
	}

	/**
	 * @return the $_primaryKey
	 */
	public function getPrimaryKey() 
	{
		return $this->_primaryKey;
	}

	/**
	 * @param unknown $_primaryKey
	 */
	public function setPrimaryKey($_primaryKey) 
	{
		$this->_primaryKey = $_primaryKey;
		return $this;
	}

	/**
	 * @return the $_keepFileName
	 */
	public function getKeepFileName() 
	{
		return $this->_keepFileName;
	}

	/**
	 * @param boolean $_keepFileName
	 */
	public function setKeepFileName($_keepFileName) 
	{
		$this->_keepFileName = $_keepFileName;
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function getFileName()
    {
        if(!$this->_fileName && !$this->_keepFileName) {
            $this->_fileName = uniqid();
        }
        
        return $this->_fileName;
    }
    
    /**
     * 
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->_fileName = $fileName;
        return $this;
    }

    /**
	 * @return the $_width
	 */
	public function getWidth() 
	{
		return $this->_width;
	}

	/**
	 * @return the $_height
	 */
	public function getHeight() 
	{
		return $this->_height;
	}

	/**
	 * @return the $_keepRatio
	 */
	public function getKeepRatio() {
		return $this->_keepRatio;
	}

	/**
	 * @return the $_destination
	 */
	public function getDestination() {
		return $this->_destination;
	}

	/**
	 * @return the $_updateDb
	 */
	public function getUpdateDb() {
		return $this->_updateDb;
	}

	/**
	 * @param number $_width
	 */
	public function setWidth($_width) 
	{
		$this->_width = $_width;
		return $this;
	}

	/**
	 * @param number $_height
	 */
	public function setHeight($_height) 
	{
		$this->_height = $_height;
		return $this;
	}

	/**
	 * @param boolean $_keepRatio
	 */
	public function setKeepRatio($_keepRatio) 
	{
		$this->_keepRatio = $_keepRatio;
		return $this;
	}

	/**
	 * @param string $_destination
	 */
	public function setDestination($_destination) 
	{
		$this->_destination = $_destination;
		return $this;
	}

	/**
	 * @param boolean $_updateDb
	 */
	public function setUpdateDb($_updateDb) 
	{
		$this->_updateDb = $_updateDb;
		return $this;
	}

	/**
	 * 
	 * @var Zend_Config
	 */
	protected $_config;
	
	public function getConfig()
	{
	    return $this->_config;
	}
	
	public function setConfig(Zend_Config $config)
	{
	    $tihs->_config = $config;
	    return $this;
	}
	
	/**
	 * 欄位名稱
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @return the $_name
	 */
	public function getName() 
	{
		return $this->_name;
	}
	
	/**
	 * @param field_type $_name
	 */
	public function setName($_name) 
	{
		$this->_name = $_name;
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
	 * @param array  $options
	 */
	public function __construct(Orbas_Controller_Action $controller, $options = array())
    {
        $this->_controller = $controller;

        if(isset($options['name'])){
            $this->setName($options['name']);
            $this->_initConfig();
        }
        
        foreach($options as $key => $option) {
            $methodName = 'set' . ucfirst($key);
            if(method_exists($this, $methodName)) {
                $this->$methodName($option);
            }
        }
    }
    
    /**
     * 自動取得精靈設定檔，並設定參數
     * 
     */
    protected function _initConfig()
    {
        $name = $this->getName();
        $config = Orbas_Config_Broker::getFieldConfig($this->getModelName(), $name);
        
        if($config->type != 'image') {
            throw new Orbas_Application_Exception($name . ' type is invalid');
        }
        
        foreach($config->params as $param => $value) {
            $methodName = 'set' . ucfirst($param);
            if(method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    /**
     * 
     * @var Zend_Form_Element_File
     */
    protected $_element;
    
    public function getElement()
    {
        if(!$this->_element) {
            $this->_element = new Zend_Form_Element_File($this->getName());
        }
        
        return $this->_element;
    }
    
    /**
     * 開始上傳
     * 
     */
    public function upload()
    {
        $name = $this->getName();
        
        if(!$name) {
            throw new Orbas_Application_Exception('field name is empty');
        }
        
        $file = $this->getElement();

        if(!realpath($this->getDestination())) {
            mkdir($this->getDestination(), 0777, true);
        }

        $destination = realpath($this->getDestination());
        $file->setDestination($destination);
        
        # 檔案名稱
        $fileName = $file->getFileName(null, false);
        
        # 副檔名
        $ext = substr($fileName, strrpos($fileName, '.'));
        
        /*
         * 檔名重設
         */
        if(!$this->getKeepFileName()) {
            $fileName = $this->getFileName();
            $file->addFilter('Rename', $fileName . $ext);
        }
        
        $file->addFilter(new Skoch_Filter_File_Resize(array(
        	'width'     => $this->getWidth(),
        	'height'    => $this->getHeight(),
        	'keepRatio' => $this->getKeepRatio(),
        )));
        
        # 圖檔限定
        if(extension_loaded('fileinfo')) {
            $file->addValidator('IsImage');
        }

        $fileSizeValidator = new Zend_Validate_File_Size(ini_get('upload_max_filesize') . 'B');
        $file->addValidator($fileSizeValidator);
        
        if(!$file->receive()) {
            throw new Orbas_Form_Validator_Exception($file->getErrorMessages());
        }
        
        # 更新資料庫
        if($this->getUpdateDb()) {

            $dbPath = $this->_getDbPath();
            
            $row = $this->getModel()->fetchRowByPrimary($this->getPrimaryKey());
            $row->$name = $dbPath . $fileName . $ext;
            $row->save();
        }
    }

    /**
     * 取得檔案路徑，提供顯示於前台使用
     * 
     */
    public function getDisplayFilePath()
    {
        $path = $this->_getDbPath();
        $fileName = $this->getElement()->getFileName(null, false);
        return $path . $fileName;
    }
    
    /**
     * 取得儲存至資料表的路徑
     *
     * @param boolean $includeSlash 路徑最後面是否包含斜線
     */
    protected function _getDbPath($includeSlash = true)
    {
        $documentRootLength = strlen($_SERVER['DOCUMENT_ROOT']);
        $destination = $this->getElement()->getDestination();
        
        $path = str_replace('\\', '/', substr($destination, $documentRootLength));
        $path = rtrim($path, '/');
        
        return $includeSlash ? $path . '/' : $path;
    }
}
?>