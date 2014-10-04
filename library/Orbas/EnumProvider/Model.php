<?php
/**
 * 提供模組的集合
 * 
 * 
 * @author Ivan
 *
 */
class Orbas_EnumProvider_Model extends Orbas_EnumProvider_Abstract
{
    /**
     * 
     * @param string $source  modelName
     * @param string $field   欄位
     * @param boolean $void   空白選項
     * @param boolean|string $multiLang 是否為序列化多語系，非布林值時則為需要的語系
     * 
     * @see Orbas_EnumProvider_Abstract::direct()
     */
    public function direct($source, $field, $void = false, $multiLang = false)
    {
        $model = $this->getModel($source);
        
        # 資料表名稱
        $tableName = $model->info(Zend_Db_Table::NAME);
        
        $select = $model->getAdapter()
                        ->select()
                        ->from($tableName, array('SN', $field));
        
        if($this->_controller && $this->_controller instanceof Orbas_EnumProvider_Interface_Model) {
            $this->_controller->onModelEnumCreate($source, $select);
        }
        
        if($multiLang === true) {
            $locale = Orbas_Translate::getLocale();
        } else if(is_string($multiLang)) {
            $locale = $multiLang;
        }
        
        $rows = $select->query()
                       ->fetchAll();
        $result = array();
        foreach($rows as $row) {

            if($multiLang){
                
                $tmpArray = unserialize($row[$field]);
                $result[$row['SN']] = $tmpArray[$locale];
                
            } else {
                $result[$row['SN']] = $row[$field];
            }
        }
        
        /*
         * 增加空白選項
         */
        if($void){
            $result = array('' => '') + $result;
        }
        
        return $result;
    }

    /**
     * 
     * @param string $name
     * @return Orbas_Model_Abstract
     */
    public function getModel($name)
    {
        return Orbas_Model_Broker::get($name);
    }
}
?>