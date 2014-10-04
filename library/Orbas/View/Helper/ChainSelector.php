<?php
/**
 * 樹狀下拉選單
 * 
 * @author Ivan
 * @link http://kotowicz.net/jquery-option-tree/demo/demo.html
 *
 */
class Orbas_View_Helper_ChainSelector extends Zend_View_Helper_FormElement
{
    /**
     * 
     * @var boolean
     */
    protected $_initialized = false;
    
    /**
     * 
     * @var array
     */
    protected $_options;
    
    /**
     * 預設參數
     * 
     * @var array
     */
    static protected $_initOptions = array(
        'choose' => ' ',
        'empty_value'   => '',
        'set_value_on'  => 'each', // we will change input value when every select box changes
        'loading_image' => '/img/ajaxLoader/14x14/loader02.gif',
        'json_in_name_value' => true
    );
    
    /**
     * 取得Json的網址
     * 
     * @var string
     */
    protected $_url;
    
    /**
     * 
     * @param string $name        欄位名稱
     * @param integer $value      值
     * @param string $module      配合使用的module名稱
     * @param string $controller  配合使用的controller名稱
     * @param string $modelName   Model Name
     * @param array  $options     jquery.optionTree.js 參數 
     */
    public function chainSelector($name, $value = null, $module, $controller, $modelName, $options = array())
    {
        if($this->_initialized === false){
            $this->_init();
        }
        
        $this->_initOptions($options);
        $url = sprintf('/%s/%s/%s/', $module, $controller, 'ajax.get.options');
        if( !isset($this->_options['on_each_change']) ) {
            $this->_options['on_each_change'] = $url;
        }

        if($value !== null) {
            $this->_options['preselect'] = array(
            	$name => $this->_getPathByValue($value, $modelName)
            );
        }
        
        $this->_createJavascript($name);
        
        # 產生Hidden值
        return $this->_hidden($name, $value, array('id' => $name));
    }
    
    /**
     * 產生套件相關JS設定
     * 
     * @param string $name
     */
    protected function _createJavascript($name)
    {
        $js = <<<JS
        $(function(){
            $.getJSON('{$this->_options['on_each_change']}', function(tree){
                $("#{$name}").optionTree(tree, {$this->getJsonOptions()}).change();
            });
        });
JS;
        
        $this->view->headScript()->appendScript($js, 'text/javascript');
    }
    
    /**
     * 參數初始化
     * 
     * @param array $options
     */
    protected function _initOptions($options)
    {
        $this->_options = array_merge(self::$_initOptions, $options);
    }

    /**
     * 取得參數設定
     * 
     * @return string JSON encoded object
     */
    public function getJsonOptions()
    {
       return Zend_Json::encode($this->_options, false, array('enableJsonExprFinder' => true)); 
    }
    
    /**
     * 類別初始化
     * 
     */
    protected function _init()
    {
        $this->view->headScript()->appendFile('/js/jquery.optionTree.js', 'text/javascript');
        
        $this->_initialized = true;
    }
    
    /**
     * 取得路徑
     * 
     * @param integer $value
     * @param string  $modelName
     * @return array
     */
    protected function _getPathByValue($value, $modelName)
    {
        $path = Orbas_Tree::getPathByValue($value, $modelName);
        
        if($path) {
            $path .= $value;
            $path = trim($path, '/');
            $paths = explode('/', $path);
            
            foreach($paths as $key => $value) {
                if(is_numeric($value)) $paths[$key] = intval($value);
            }
            
            return $paths;
        }
        
        return array();
    }
}
?>