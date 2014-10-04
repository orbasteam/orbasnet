<?php
/**
 * 修改zend_captcha_image 文字顯示方式
 *  
 * @author Ivan
 * 
 */
class Orbas_Captcha_Image extends Zend_Captcha_Image
{
	/**
	 * 照片中隨機劃線的數量
	 * @var integer
	 */
	public static $confuseDegree = 8;
	
	protected function _generateImage($id, $word)
	{
        if (!extension_loaded("gd")) {
			require_once 'Zend/Captcha/Exception.php';
   			throw new Zend_Captcha_Exception("Image CAPTCHA requires GD extension");
		}

		if (!function_exists("imagepng")) {
			require_once 'Zend/Captcha/Exception.php';
			throw new Zend_Captcha_Exception("Image CAPTCHA requires PNG support");
		}

		if (!function_exists("imageftbbox")) {
			require_once 'Zend/Captcha/Exception.php';
			throw new Zend_Captcha_Exception("Image CAPTCHA requires FT fonts support");
		}

		$font = $this->getFont();

		if (empty($font)) {
			require_once 'Zend/Captcha/Exception.php';
			throw new Zend_Captcha_Exception("Image CAPTCHA requires font");
		}

		$w = $this->getWidth();
		$h = $this->getHeight();
		$fsize = $this->getFontSize();
		
		$img_file = $this->getImgDir() . $id . $this->getSuffix();
		if(empty($this->_startImage)) {
			$image = imagecreatetruecolor($w, $h);
		} else {
			$image = imagecreatefrompng($this->_startImage);
			if(!$image) {
				require_once 'Zend/Captcha/Exception.php';
				throw new Zend_Captcha_Exception("Can not load start image");
			}
			$w = imagesx($image);
			$h = imagesy($image);
		}
		
		// Define some common colors
		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$red   = imagecolorallocatealpha($image, 255, 150, 150, 75);
		$green = imagecolorallocatealpha($image, 150, 255, 150, 75);
		$blue  = imagecolorallocatealpha($image, 150, 150, 255, 75);
		$gray  = imagecolorallocate($image, 75, 75, 75);
		$line  = imagecolorallocate($image, 120, 120, 120);
		
		imagefilledrectangle($image, 0, 0, $w, $h, $white);
		
		// Ellipses (helps prevent optical character recognition)
		imagefilledellipse($image, ceil(rand(5, $w - 5)), ceil(rand(0, $h - 5)), $h, $h, $red);
		imagefilledellipse($image, ceil(rand(5, $w - 5)), ceil(rand(0, $h - 5)), $h, $h, $green);
		imagefilledellipse($image, ceil(rand(5, $w - 5)), ceil(rand(0, $h - 5)), $h, $h, $blue);	

		// Borders
		imagefilledrectangle($image, 0, 0, $w, 0, $black);
		imagefilledrectangle($image, $w - 1, 0, $w - 1, $h - 1, $black);
		imagefilledrectangle($image, 0, 0, 0, $h - 1, $black);
		imagefilledrectangle($image, 0, $h - 1, $w, $h - 1, $black);
		
		for ($i=0; $i<self::$confuseDegree; $i++)  {
			imageline(
				$image, 
				rand(5, $w - 5), 
				rand(5, $h - 5), 
				rand(5, $w - 5), 
				rand(5, $h - 5), 
				$line
			); 
		}
		
		$textbox = imageftbbox($fsize, 0, $font, $word);
		$x = ($w - ($textbox[2] - $textbox[0])) / 2;
		$y = ($h - ($textbox[7] - $textbox[1])) / 2;
		imagefttext($image, $fsize, rand(-10, 10), $x, $y, $gray, $font, $word);
		imagepng($image, $img_file);
    }
    
    /**
     * override
     * Generate new random word (Only number)
     *
     * @return string
     */
    protected function _generateWord()
    {
    	$number = sprintf('%04d', rand(0, 9999));
    	return $number;
    }
}
?>