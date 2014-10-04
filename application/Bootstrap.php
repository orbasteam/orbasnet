<?php

class Bootstrap extends Orbas_Bootstrap_BootStrap
{
    protected function _initSessionDb()
    {
        return;
        
        $config = $this->getOption('resources');
        if(isset($config['db'])) {

            $db = Zend_Db::factory($config['db']['adapter'], $config['db']['params']);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
            $handlerConfig = array(
                'name'           => 'session',
                'primary'        => 'ID',
                'modifiedColumn' => 'MODIFIED',
                'dataColumn'     => 'DATA',
                'lifetimeColumn' => 'LIFETIME'
            );
            
            Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($handlerConfig));
            Zend_Session::start();
        }
    }

}

