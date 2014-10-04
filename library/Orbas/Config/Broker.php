<?php
/**
 * 取得資料設定檔
 * 
 * @author Ivan
 *
 */
class Orbas_Config_Broker
{
    /**
     * 
     * @var array
     */
    static protected $_configs = array();
    
    /**
     * 
     * @param string $name
     * @return Zend_Config
     */
    static public function get($name)
    {
        if(!isset(self::$_configs[$name])){
            
            $configName = explode('_', $name);
            $path      = APPLICATION_PATH . '/configs/model/' . ucfirst($configName[0]) . '/' . ucfirst($configName[1]) . '.php';

            if(!file_exists($path)){
                throw new Orbas_Application_Exception('No config "' .  ucfirst($configName[0]) . '/' . ucfirst($configName[1]) . '" file');
            }
            
            self::$_configs[$name] = new Zend_Config(require($path));
        }
        
        return self::$_configs[$name];
    }
    
    /**
     * 直接取得欄位設定
     * 
     * @param string $name
     * @param string $field
     */
    static public function getFieldConfig($name, $field)
    {
        $config = self::get($name);
        if(!isset($config->fields->$field)) {
            throw new Orbas_Application_Exception("$field in $name config is not setting.");
        }
        
        return $config->fields->$field;
    }
}
?>