<?php
class View_Helper_SystemMenu extends Zend_View_Helper_Navigation_Menu
{
	/**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  Zend_Navigation_Container $container  [optional] container to
     *                                               operate on
     * @return Zend_View_Helper_Navigation_Menu      fluent interface,
     *                                               returns self
     */
    public function systemMenu(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
        
        return $this;
    }
    
    public function renderMenu()
    {
        $html = '<ul class="navigation">';
    	$html .= $this->_renderMenu($this->getContainer());
    	$html .= '</ul>';
    	
    	return $html;
    }
    
    protected function _renderMenu(Zend_Navigation_Container $container)
    {
    	$html = '';
    	
    	if($container){

	    	foreach($container as $content){
	    		
	    	    $html .= '<li>';
	    	    $html .= '<a href="' . $content->getHref() . '">' . $content . '</a>';
	    	    
	    	    if($content->hasChildren()){
	    	        
	    	        $html .= '<ul class="subMenu">';
	    	        $html .= $this->_renderMenu($content);
	    	        $html .= '</ul>';
	    	    }
	    	    
	    	    $html .= '</li>';
	    	}
    	}
    	
    	return $html;
    }
    
}
?>