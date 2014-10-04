<?php
interface Orbas_Form_Validator_Interface
{
    /**
     * 自訂表單驗證條件
     * 
     * @param Zend_Form $form
     */
    public function setupCustomFormValidator(Zend_Form $form, Orbas_Form_Validator $validator);
}
?>