<?php

class Orbas_EnumProvider_Enum extends Orbas_EnumProvider_Abstract
{
	/**
	 * 
	 * @param string $name
	 * @see Orbas_EnumProvider_Abstract::direct()
	 */
	public function direct($name, $multilingual = false) 
	{
	    if(Zend_Registry::isRegistered($name) === false) {
	        $this->_requireRegistry();
	    }
	    
	    if($multilingual) {
	        return $this->_multiLingualEnum($name);
	    }
	    
	    return Zend_Registry::get($name);
	}
	
	/**
	 * 轉換多語系
	 * 
	 * @param string $name
	 */
	protected function _multiLingualEnum($name)
	{
	    $locale     = Orbas_Translate::getLocale();
	    $translator = Zend_Registry::get('Zend_Translate');

	    $enums = Zend_Registry::get($name);
	    if($enums) {
	        foreach($enums as $key => $enum) {
	            $enums[$key] = $translator->_($enum, $locale);
	        }
	    }
	    
	    return $enums;
	}
	
	protected function _requireRegistry()
	{
	    require_once APPLICATION_PATH . '/../data/enums/global.php' ;;
	}
    
}
?>