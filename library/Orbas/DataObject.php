<?php
/**
 * 存放資料的物件
 * 通常用於取得資料時，如果為空值，一般物件會產生notice訊息
 * 用該物件則回傳null
 * 
 * @author Ivan
 *
 */
class Orbas_DataObject implements ArrayAccess, Countable, Iterator
{
    /**
     * 
     * @var array
     */
    protected $_data;
    
    /**
     * 
     * 
     * @param mixed $data
     */
    public function __construct($data = array())
    {
        if(is_object($data) && is_callable(array($data, 'toArray'))){
            $this->_data = $data->toArray();
        } else if(is_array($data)){
            $this->_data = $data;
        }
    }
    
    /**
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
       $this->set($name, $value);
    }

    /**
     * 設定資料值
     * 
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) 
    {
        $this->_data[$name] = $value;
        return $this;
    }
    
    /**
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
       return $this->get($name);
    }
    
    /**
     * 取得資料值
     * 
     * @param string  $name
     * @param boolean $toArray
     * @return mixed
     */
    public function get($name, $toArray = false)
    {
        if(!isset($this->_data[$name])){
            return null;
        }
        
        if($toArray){
            return explode(',', $this->_data[$name]);
        }

        return $this->_data[$name];
    }
    
    /**
     * 
     * @param string $name
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }
    
    /**
     * 
     * @param string $name
     */
    public function __unset($name)
    {
        if(isset($this->_data[$name])){
            unset($this->_data[$name]);
        }
    }
    
	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) 
	{
		return $this->__isset($offset);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) 
	{
	    return $this->get($offset);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value) 
	{
		$this->__set($offset, $value);
	}

	/* (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) 
	{
		$this->__unset($offset);
	}
	
	/* (non-PHPdoc)
	 * @see Countable::count()
	 */
	public function count() 
	{
		return count($this->_data);
	}
	
	public function toArray()
	{
	    return $this->_data;
	}
	
	 /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index;
	
	/* (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current() 
	{
	    return current($this->_data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() 
	{
		return key($this->_data);
	}

	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() 
	{
		next($this->_data);
		$this->_index++;
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() 
	{
	    reset($this->_data);
	    $this->_index = 0;
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() 
	{
	    return $this->_index < $this->count();
	}
}
?>