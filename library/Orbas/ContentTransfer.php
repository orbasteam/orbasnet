<?php
/**
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_ContentTransfer
{
    /**
     * 
     * @var array
     */
    protected $_plugins;
    
    public function __construct()
    {
    	$this->_initPlugins();
    }
    
    protected function _initPlugins()
    {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('contentTranfer');
        
        if(isset($options['plugins'])) {
            foreach($options['plugins'] as $name) {
                $this->_plugins[$name] = new $name();
            }
        }
    }
    
    /**
     * 
     * @param string $content
     */
    public function tranfer($content)
    {
        if($this->_plugins) {
            foreach($this->_plugins as $plugin) {
                $result = $plugin->tranfer($content);
                if($result !== false) {
                    return $result;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * @param unknown $class
     * @return Orbas_ContentTransfer_Interface
     */
    public function getPlugin($class)
    {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return $plugin;
            }
        }
        
        return false;
    }
}
?>