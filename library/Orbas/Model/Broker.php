<?php
/**
 * 
 * 提供取得 Model 物件
 *
 */
class Orbas_Model_Broker
{
    static protected $_models = array();
	
	/**
	 * 取得其他模組
	 *
	 * @param string $name
	 * @return Orbas_Model_Abstract
	 */
	static function get($name) {
		
		$name = ucfirst($name);
		
		if (!isset(self::$_models[$name])) {		
		
    		$modelName = $name; 
    		if (strpos($modelName, 'Model') === false) {
    			$modelName .= 'Model';
    		}
    		
    		$explodeModelName = explode('_', $name);
    		$modelPath = APPLICATION_PATH . '/models/' . ucfirst($explodeModelName[0]) . '/' . ucfirst($explodeModelName[1]) . 'Model.php';
    		if(!file_exists($modelPath)){
    		    throw new Orbas_Application_Exception($modelName . ' is not existing');
    		}
    		
    		Zend_Loader::loadClass($modelName);
    		self::$_models[$name] = new $modelName();    				    	
		}
		return self::$_models[$name];
	}
}
?>