<?php
/**
 * 解析時間轉換成幾天前、幾秒前...等資訊
 * 
 * @author Ivan
 *
 */
class View_Helper_TimeParse extends \Zend_View_Helper_Abstract
{
    /**
	 * 時間顯示 (秒  => 顯示)
	 */
	protected $_timeDisplay = array(
		'60' 		=> 'second',
		'3600'		=> 'minute',
		'86400'		=> 'hour',
		'2592000'	=> 'day',
		'31104000'	=> 'month',
	);

	/**
	 * 
	 * @param string $time
	 */
	public function timeParse($time)
	{
		$postTime = time() - strtotime($time);
		$display  = '';
		
		foreach($this->_timeDisplay as $seconds => $text){
			
			if($postTime < $seconds){
				
				# 計算時間除以的秒數
				$divisionSeconds = isset($prevSeconds) ? $prevSeconds :  1;
				
				# 計算時間
				$calcTime = floor($postTime / $divisionSeconds);

				if($calcTime == 0){
					$display = 'just';
				} else{
					# 如果計算出來的時間大於1 文字敘述要加s
					$text = $calcTime > 1 ? $text . 's' : $text;
					$display  = $calcTime . ' ' . $text . ' ago';
				}			
				
				break;
			}
			
			$prevSeconds = $seconds;
		}
		return $display;
	}
}
?>