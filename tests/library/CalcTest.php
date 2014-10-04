<?php
require_once 'library/Orbas/Calc.php';

class Orbas_CalcTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     * @dataProvider provider
     */
    public function testCalc($a, $b, $result)
    {
        $calc = new Orbas_Calc();
       
        $this->assertEquals($result, $calc->calc($a, $b));
    }
    
    public function provider() 
    {
        return array(
        	array(10, 30, 40),
            array(50, 50, 100),
            array(-10, 10, 0),
            array(1, 2, 3),
            array(-1, -2, -3)
        );
    }
}
?>