<?php
class Orbas_Form_Validator_Exception extends Exception
{
    /**
     * 
     * @var array
     */
    protected $_messages;
    
    /**
     * 欄位設定檔
     * 
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     * 
     * @param string|array $messages
     * @param Zend_Config|array $config 
     */
    public function __construct ($messages, $config = null)
    {
        $messages = (array) $messages;
        
        $this->_messages = $messages;
        $this->_config = $config;
    }
    
    /**
     * 
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
    
    /**
     * 轉為字串
     * 
     * @see Exception::__toString()
     */
    public function __toString()
    {
        $string = '';
        $fieldsConfig = $this->_config->fields;
        foreach($this->_messages as $key => $messages) {
            
            $fieldName = isset($fieldsConfig->$key) ? $fieldsConfig->$key->name : $key;
            foreach($messages as $message) {
                $string .= $fieldName . ' ' . $message . '\n';
            }
        }
        
        return $string;
    }
}
?>