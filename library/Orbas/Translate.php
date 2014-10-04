<?php

/**
 * 翻譯工具
 * 
 * @author Ivan
 *
 */
class Orbas_Translate extends Zend_Controller_Action_Helper_Abstract
{
    # 存放目前使用者的語系的session命名空間
    const SESSION_NAMESPACE = 'orbasLocaleNamespace';
    
    /**
     * 
     * @var Zend_Translate
     */
    protected $_translate;
    
    /**
     * @return the $_translate
     */
    public function getTranslate() 
    {
        if(!$this->_translate){
            $this->_translate = new Zend_Translate(
                array(
                	'adapter'   => self::getTranslateAdpater(),
                	'content'   => APPLICATION_PATH . '/../data/locale/locale_' . self::getLocale() . '.php',
                    'locale'    => self::getLocale()
                )
            );
        }
        
    	return $this->_translate;
    }
    
    /**
     * @param Zend_Translate $_translate
     */
    public function setTranslate($_translate) 
    {
    	$this->_translate = $_translate;
    	return $this;
    }
    
    /**
     * 翻譯轉接器
     * 預設值
     * 
     * @var string
     */
    static protected $_translateAdpater = 'Zend_Translate_Adapter_Array';
    
	/**
     * 設定翻譯轉接器
     * 
     * @param string $name
     */
    static public function setTranslateAdapter($name)
    {
        self::$_translateAdpater = $name;
    }
    
    /**
     * 
     * @return string the $_translateAdapter
     */
    static public function getTranslateAdpater()
    {
        return self::$_translateAdpater;
    }
    
    /**
     * 翻譯
     * 
     * @param string $messageId  翻譯字串(或者ID)
     * @param string $locale     語系 
     */
    public function _($messageId, $locale = null)
    {
        return $this->getTranslate()->_($messageId, $locale);
    }
    
    /**
     * proxy to $this->_()
     * 
     * @param string $messageId  翻譯字串(或者ID)
     * @param string $locale     語系 
     */
    public function translate($messageId, $locale = null)
    {
        return $this->_($messageId, $locale);
    }
    
    /**
     * 目前語系
     * 
     * @var string
     */
    static protected $_locale = 'zh_TW';
    
    static public function setLocale($locale)
    {
        self::$_locale = $locale;
        
        $session = Zend_Controller_Action_HelperBroker::getStaticHelper('Session');
        $session->set(self::SESSION_NAMESPACE, $locale);
    } 
    
    static public function getLocale()
    {
        return self::$_locale;
    }
}
?>