<?php
/**
 * 登入物件外掛介面
 * 
 * @author Ivan
 *
 */
interface Orbas_Auth_Plugin_Interface
{
    /**
     * 執行登入前
     * 
     * @param Orbas_Auth $auth
     * @param array  $data
     * @param string  $role
     */
    public function preLogin(Orbas_Auth $auth, $data, $role);
    
    /**
     * 登入失敗時
     * 
     * @param Orbas_Auth $auth
     * @param Orbas_Auth_Exception $exception
     * @param array  $data
     * @param string $role
     */
    public function loginFailure(Orbas_Auth $auth, Orbas_Auth_Exception $exception, $data, $role);
    
    /**
     * 登入成功時
     * 
     * @param Orbas_Auth $auth
     * @param array  $data
     * @param string $role
     */
    public function loginSuccess(Orbas_Auth $auth, $data, $role);
    
    /**
     * 登出時
     * 
     * @param string $role
     */
    public function preLogout($role);
}
?>