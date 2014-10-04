<?php

/**
 * 縮址
 * 
 * @author Ivan
 *
 */
class ShortenUrlController extends Orbas_Controller_Action
{
    public function urlAction()
    {
        $url = $this->getParam('url');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.googleapis.com/urlshortener/v1/url");
        curl_setopt($ch, CURLOPT_POST, 1); // 啟用POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('longUrl' => $url)) );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch); 
        curl_close($ch);

        if($result) {
            echo $result;
        }
        
        exit;
    }
}
?>