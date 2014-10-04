<?php
require_once 'library/Zend/Test/PHPUnit/ControllerTestCase.php';

class BoardControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        
        parent::setUp();
    }
    
    public function appBootstrap()
    {
        $this->application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        $this->application->bootstrap();
    }
    
    public function testDetailAction()
    {
        $this->dispatch('/default/board/detail');
        $this->request->setQuery(array(
        	'sn' => 0
        ));
        
        $this->assertModule('default');
        $this->assertController('board');
        $this->assertAction('detail');
    }
    
    public function testListAction()
    {
        
    }
}
?>