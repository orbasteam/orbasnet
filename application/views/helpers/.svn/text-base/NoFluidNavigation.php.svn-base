<?php
class View_Helper_NoFluidNavigation extends Zend_View_Helper_Navigation_Menu
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
    public function NoFluidNavigation(Zend_Navigation_Container $container = null)
    {
    	if (null !== $container) {
    		$this->setContainer($container);
    	}
    
    	return $this;
    }
    
    public function renderMenu()
    {
    	$html = '<ul class="noFluidNav">';
    	$html .= $this->_renderMenu($this->getContainer());
    	$html .= '</ul>';
    	 
    	return $html;
    }
    
    protected function _renderMenu(Zend_Navigation_Container $container)
    {
    	$html = '';
    	 
    	if($container){
    
    		foreach($container as $content){
    	   
    		    $class = '';
    		    if($content->isActive()){
    		        $class = 'active';
    		    }
    		    
    			$html .= '<li class="' . $class .  '">';
    			$html .= '<a href="' . $content->getHref() . '">';
    			$html .= $content;
    			
    			if($content->hasChildren()){
    			    $html .= '<span>+</span>';
    			}
    			
    			$html .= '</a>';
    
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