<?php
/**
 * 欄位條件過濾
 * 
 * @author Ivan
 *
 */
abstract class Orbas_Form_Filter_Abstract
{
    /**
     * 
     * @var Orbas_DataObject
     */
    protected $_data;
    
    public function __construct(Orbas_DataObject $data)
    {
        $this->_data = $data;
    }

    /**
     * 過濾資料
     * 
     * @param string $field
     * @return void 因為$this->_data 是物件，因此直接設定至$this->_data中即可
     */
    abstract public function filter($field);
}
?>