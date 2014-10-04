<?php
/**
 * 產生樹狀結構
 * 
 * @author Ivan
 *
 */
class Orbas_Tree_Structure
{
    protected $_data;
    
    /**
     * 
     * @param array|ArrayObject $data
     * @throws Orbas_Application_Exception
     */
    public function __construct($data)
    {
        if(is_array($data)){
            $this->_data = new Orbas_DataObject($data);
        } else if(!$data instanceof ArrayAccess) {
            throw new Orbas_Application_Exception('Tree Data is not instance of array or ArrayAccess');
        }
        
        $this->_data = $data;
    }
    
    /**
     * 開始產生樹狀結構
     * 
     * @param string $data
     * @param string $result
     * @return ArrayObject
     */
    public function create($data = null, $result = null)
    {
        if($data === null) {
            $data = $this->_data;
        }
        
        if($result === null) {
        	$result = new ArrayObject(array());
        }
        
        # 尚未放入Result陣列中的資料
        $unassigned = new ArrayObject(array());
        
        foreach($data as $row) {
            
            if($row instanceof Zend_Db_Table_Row) {
                $row = $row->toArray();
            }
            
            if($row['PARENT_SN'] == 0) {
                $result[$row['SN']] = new ArrayObject($row);
            } else {
                $path = trim($row['PATH'], '/');
        		$path = strpos($path, '/') === false ? (array)$path : explode('/', $path);
        		$node = $result;
    
    		    $unassignedFlag = 0;
        		foreach($path as $parentSN) {
        		    
                	if(isset($node['subitems']) && isset($node['subitems'][$parentSN])){
                		$node = $node['subitems'][$parentSN];
                    } else if (isset($node[$parentSN])) {
                        $node = $node[$parentSN];
                    } else {
                    	$unassignedFlag++;
                    }
            	}
        
            	if($unassignedFlag == count($path)){
            		$unassigned[] = $row;
            	} else {
            		if(!isset($node['subitems'])) {
            	    	$node['subitems'] = new ArrayObject(array());
                    }
            
          			$node['subitems'][$row['SN']] = new ArrayObject($row);
          		}
        	}
        }
        
        if(count($unassigned) == 0) {
            return $result;
        }
        
        return $this->create($unassigned, $result);
    }
}
?>