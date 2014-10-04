<?php

/**
 * 集合提供器
 * 
 * 提供表單所需的集合
 * 使用方法為 
 *      $this->getHelper('EnumProvider')->model('source', 'field');
 *      
 *  model 為使用的集合，目前有enum、model
 * 
 * @author Ivan
 *
 */
class Orbas_EnumProvider extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * 
     * @var array
     */
    protected $_provider;
    
    public function __call($name, $arguments)
    {
        if(isset($this->_provider[$name])){
            $instance = $this->_provider[$name];
        }  else {
            $path = realpath(APPLICATION_PATH . '/../library/Orbas/EnumProvider') . DIRECTORY_SEPARATOR . ucfirst($name) . '.php';
            if(!file_exists($path)){
            	throw new Orbas_Application_Exception("No such kind of $name enums");
            }
            
            $objectName = 'Orbas_EnumProvider_' . ucfirst($name);
            $instance = new $objectName($this->getActionController());
            
            $this->_provider[$name] = $instance;
        }
        
        return call_user_func_array(array($instance, 'direct'), $arguments);
    }
}
?>