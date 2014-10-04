<?php
/**
 * 轉換Youtube
 * 
 * @author Ivan
 *
 */
class Orbas_ContentTransfer_Youtube implements Orbas_ContentTransfer_Interface
{
    /**
     * 
     * @var array
     */
    protected $_pattern = array(
        '/(http|https):\/\/www\.youtube\.com\/watch\?v=([\w|-]+)/',
    	'/(http|https):\/\/youtu.be\/([\w|-]+)/'
    );
    
    /**
     * (non-PHPdoc)
     * @see Orbas_ContentTranfer_Interface::tranfer()
     */
    public function tranfer ($content)
    {
        foreach($this->_pattern as $pattern) {
            if(preg_match($pattern, $content)) {
                $replacement = "\n<iframe width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>\n";
                $youtubeCount = 0;
                
                return preg_replace($pattern, $replacement, $content, 1, $youtubeCount);
            }
        }
        
        return false;
    }
    
	/* (non-PHPdoc)
	 * @see Orbas_ContentTranfer_Interface::getInformMessage()
	 */
	public function getInformMessage() 
	{
		return Orbas_Auth::getUser('NAME', ROLE_MEMBER) . ' 新增了1部影片';
	}
}
?>