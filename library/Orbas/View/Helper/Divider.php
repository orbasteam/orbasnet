<?php
/**
 * 後台分隔線
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_Divider extends Zend_View_Helper_Abstract
{
    public function divider()
    {
        $html  = '<div class="divider">';
        $html .= '<div><span></span></div>';
        $html .= '</div>';
        
        return $html;
    }
}
?>