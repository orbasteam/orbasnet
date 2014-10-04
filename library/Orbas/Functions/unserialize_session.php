<?
if(!function_exists('unserialize_session')) {
    function unserialize_session( $data ) {
        if(  strlen( $data) == 0)
        {
            return array();
        }
        
        // match all the session keys and offsets
        preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matchesarray, PREG_OFFSET_CAPTURE);
    
        $returnArray = array();
    
        $lastOffset = null;
        $currentKey = '';
        foreach ( $matchesarray[2] as $value )
        {
            $offset = $value[1];
            if(!is_null( $lastOffset))
            {
                $valueText = substr($data, $lastOffset, $offset - $lastOffset );
                $returnArray[$currentKey] = unserialize($valueText);
            }
            $currentKey = $value[0];
    
            $lastOffset = $offset + strlen( $currentKey )+1;
        }
    
        $valueText = substr($data, $lastOffset );
        $returnArray[$currentKey] = unserialize($valueText);
        
        return $returnArray;
    } 
}

?>