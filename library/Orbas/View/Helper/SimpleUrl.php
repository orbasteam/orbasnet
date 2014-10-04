<?php
/**
 * 產生連結
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_SimpleUrl extends Zend_View_Helper_Abstract
{
    /**
     * Create URL based on default route
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @return string
     */
    public function simpleUrl($action, $controller = null, $module = null, array $params = null)
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        return $helper->simple($action, $controller, $module, $params);
    }
}
?>