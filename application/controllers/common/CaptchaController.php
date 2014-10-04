<?php
/**
 * 驗證碼
 * 
 * @author Ivan
 *
 */
class Common_CaptchaController extends Orbas_Controller_Action
{
	public function indexAction()
	{
		$this->getHelper('Captcha')->output();
	}
}
?>