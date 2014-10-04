<?php
/**
 * 圖片轉碼base64
 * 
 * @author Ivan
 *
 */
class Orbas_View_Helper_ImageBase64 extends Zend_View_Helper_Abstract
{
    public function imageBase64($file)
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);
        $data = file_get_contents($file);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
?>