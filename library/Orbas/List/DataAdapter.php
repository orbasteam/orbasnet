<?php
/**
 * 提供轉換數值為對應的文字使用
 * 
 * @author Ivan
 *
 */
abstract class Orbas_List_DataAdapter 
{
	/**
	 * 
	 * @var Orbas_List
	 */
	protected $_list;
	
	/**
	 * 欄位設定檔
	 * 
	 * @var array
	 */
	protected $_config;
	
	/**
	 * 鍵值對應陣列
	 *
	 * @var array
	 */
	protected $_data;
	
	public function __construct(Orbas_List $list, $config)
	{
		$this->_list   = $list;
		$this->_config = $config;
	}
	
	protected $_foreignValue = array();
	
	/**
	 * 新增對應的外部數值
	 * 例如：1 = Male，1就是ForeignValue
	 * 
	 * @param integer|string
	 */
	public function addForeignValue($value)
	{
	    $this->_foreignValue[] = $value;
	    return $this;
	}
	
	/**
	 * 取得對應文字
	 * 
	 */
	abstract public function getText($key);
}

?>