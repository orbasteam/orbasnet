<?php
/**
 * Model 抽象物件
 * 
 * @author Ivan
 */
abstract class Orbas_Model_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * 新增資料
	 * 
	 * 自動過濾資料表沒有的欄位
	 * 
	 * @param array|Orbas_DataObject $data
	 * @return integer 
	 */
	public function append($data)
	{
	    if($data instanceof Orbas_DataObject){
	        $data = $data->toArray();
	    }
	    
		$data = $this->filterCols($data);
		
		return $this->insert($data);
	}
	
	/**
	 * 以主鍵修改資料
	 * 
	 * 自動過濾資料表沒有的欄位
	 * 
	 * @param array $data
	 */
	public function updateByPrimary($data)
	{
	    if($data instanceof Orbas_DataObject){
	    	$data = $data->toArray();
	    }
	    
	    $primaryKeys = $this->info(self::PRIMARY);
	    $data = $this->filterCols($data);
	    
	    foreach($primaryKeys as $primaryKey) {
	        
	        if(!isset($data[$primaryKey])){
	            throw new Orbas_Application_Exception('No primary key "' . $primaryKey . '" in the data');
	        }
	        
	        $where[] = $primaryKey . ' = ' . $data[$primaryKey];
	    }
	    
	    $this->update($data, implode(' AND ', $where));
	}
	
	/**
	 * 過濾資料表沒有的欄位
	 * 
	 * @param array $data
	 */
	public function filterCols($data)
	{
	    $cols = $this->_getCols();
	     
	    $dataKeys = array_keys($data);
	    foreach($dataKeys as $key){
	    	if(!in_array($key, $cols)){
	    		unset($data[$key]);
	    	}
	    }
	    
	    return $data;
	}
	
	/**
	 * 以主鍵搜尋資料
	 * 
	 * @param integer $sn
	 * @return Zend_Db_Table_Row|null
	 */
	public function fetchRowByPrimary($sn)
	{
	    $row = $this->find($sn);
	    
	    if(count($row)){
            return $row[0];
	    }
	    
	    return null;
	}

	/**
	 * 取得單一欄位值
	 * 
	 * @param string $column
	 * @param string $where
	 * @return string|false
	 */
	public function fetchOne($column, $where = null)
	{
	    $select = $this->select();
	    $select->from($this->_name, array($column));
	    
	    if($where) {
	        $select->where($where);
	    }
	    
	    return $this->getAdapter()->fetchOne($select);
	}
}
?>