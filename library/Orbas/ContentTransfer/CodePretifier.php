<?php
/**
 * Code Pretifier
 * 
 * @author Ivan
 *
 */
class Orbas_ContentTransfer_CodePretifier implements Orbas_ContentTransfer_Interface
{
    public function tranfer ($content)
    {
    	$pattern = '/```\n([\w|\W]+)\n```/';
    	if(preg_match($pattern, $content)) {
    	    
    	    $replacement = '<pre class="prettyprint">$1</pre>';
    	    return preg_replace($pattern, $replacement, $content);
    	}
    	
    	return false;
    }
    
	/* (non-PHPdoc)
	 * @see Orbas_ContentTranfer_Interface::getInformMessage()
	 */
	public function getInformMessage() 
	{
		// TODO Auto-generated method stub
	}
}
?>