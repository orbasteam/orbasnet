<?php
class AngularController extends Orbas_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        
        
        if($this->getRequest()->isPost()) {
            $this->dump($this->getRequest()->getPost());
        }
    }
}
?>