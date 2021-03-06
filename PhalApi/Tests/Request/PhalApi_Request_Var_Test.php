<?php
/**
 * PhpUnderControl_PhalApiRequestVar_Test
 *
 * 针对 ../../PhalApi/Request/Var.php PhalApi_Request_Var 类的PHPUnit单元测试
 *
 * @author: dogstar 20141012
 */

require_once dirname(__FILE__) . '/../test_env.php';

if (!class_exists('PhalApi_Request_Var')) {
    require dirname(__FILE__) . '/../../PhalApi/Request/Var.php';
}

class PhpUnderControl_PhalApiRequestVar_Test extends PHPUnit_Framework_TestCase
{
    public $coreRequestVar;

    protected function setUp()
    {
        parent::setUp();

        $this->coreRequestVar = new PhalApi_Request_Var();
    }

    protected function tearDown()
    {
    }


    /**
     * @group testFormat
     */ 
    public function testFormat()
    {
        $varName = 'testKey';
        $rule = array('type' => 'int', 'default' => '2014');
        $params = array();

        $rs = PhalApi_Request_Var::format($varName, $rule, $params);

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatString
     */ 
    public function testFormatString()
    {
        $value = 2014;
        $rule = array('name' => 'testKey');

        $rs = PhalApi_Request_Var::formatString($value, $rule);

        $this->assertSame('2014', $rs);
    }


    /**
     * @group testFormatString
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testFormatStringWithRuleExceptionMinGtMax()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'min' => 9, 'max' => 5);

        $rs = PhalApi_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatString
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testFormatStringWithParamExceptionLtMin()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'min' => 8, );

        $rs = PhalApi_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatString
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testFormatStringWithParamExceptionGtMax()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', 'max' => 2, );

        $rs = PhalApi_Request_Var::formatString($value, $rule);
    }

    /**
     * @group testFormatInt
     */ 
    public function testFormatInt()
    {
        $value = '2014';
        $rule = array('name' => 'testKey', );

        $rs = PhalApi_Request_Var::formatInt($value, $rule);

        $this->assertSame(2014, $rs);
    }

    /**
     * @group testFormatFloat
     */ 
    public function testFormatFloat()
    {
        $value = '3.14';
        $rule = array('name' => 'testKey', );

        $rs = PhalApi_Request_Var::formatFloat($value, $rule);

        $this->assertSame(3.14, $rs);
    }

    /**
     * @dataProvider provideDataForFormatBoolean
     * @group testFormatBoolean
     */ 
    public function testFormatBoolean($oriValue, $expValue)
    {
        $value = $oriValue;
        $rule = array();

        $rs = PhalApi_Request_Var::formatBoolean($value, $rule);

        $this->assertSame($expValue, $rs);
    }

    public function provideDataForFormatBoolean()
    {
        return array(
            array('on', true),
            array('yes', true),
            array('true', true),
            array('success', true),
            array('false', false),
            array('1', true),
            );
    }

    /**
     * @group testFormatDate
     */ 
    public function testFormatDate()
    {
        $value = '2014-10-01 12:00:00';
        $rule = array('format' => 'timestamp');

        $rs = PhalApi_Request_Var::formatDate($value, $rule);

        $this->assertTrue(is_numeric($rs));
        $this->assertSame(1412136000, $rs);
    }

    public function testFormatDateIllegal()
    {
        $value = '2014-99-99 XX:XX:XX';
        $rule = array('format' => 'timestamp');

        $rs = PhalApi_Request_Var::formatDate($value, $rule);
        $this->assertEquals(0, $rs);
    }

    /**
     * @group testFormatArray
     */ 
    public function testFormatArrayWithJson()
    {
        $arr = array('age' => 100, 'sex' => 'male');
        $value = json_encode($arr);
        $rule = array('format' => 'json');

        $rs = PhalApi_Request_Var::formatArray($value, $rule);

        $this->assertSame($arr, $rs);
    }

    public function testFormatArrayWithExplode()
    {
        $value = '1|2|3|4|5';
        $rule = array('format' => 'explode', 'separator' => '|');

        $rs = PhalApi_Request_Var::formatArray($value, $rule);

        $this->assertEquals(array(1, 2, 3, 4, 5), $rs);
    }

    public function testFormatArrayDefault()
    {
        $value = 'phalapi';
        $rule = array();

        $rs = PhalApi_Request_Var::formatArray($value, $rule);
        $this->assertEquals(array('phalapi'), $rs);
    }

    /**
     * @group testFormatEnum
     */ 
    public function testFormatEnum()
    {
        $value = 'ios';
        $rule = array('range' => array('ios', 'android'));

        $rs = PhalApi_Request_Var::formatEnum($value, $rule);

        $this->assertSame('ios', $rs);
    }

    /**
     * @group testFormatEnum
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testFormatEnumWithRuleException()
    {
        $value = 'ios';
        $rule = array('name' => 'testKey');

        $rs = PhalApi_Request_Var::formatEnum($value, $rule);
    }

    /**
     * @group testFormatEnum
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testFormatEnumWithParamException()
    {
        $value = 'pc';
        $rule = array('name' => 'testKey', 'range' => array('ios', 'android'));

        $rs = PhalApi_Request_Var::formatEnum($value, $rule);
    }

    public function testFormatAllTypes()
    {
        $params = array(
            'floatVal' => '1.0',
            'booleanVal' => '1',
            'dateVal' => '2015-02-05 00:00:00',
            'arrayVal' => 'a,b,c',
            'enumVal' => 'male',
        );

        $rule = array('name' => 'floatVal', 'type' => 'float');
        $rs = PhalApi_Request_Var::format('floatVal', $rule,  $params);
        $this->assertSame(1.0, $rs);

        $rule = array('name' => 'booleanVal', 'type' => 'boolean');
        $rs = PhalApi_Request_Var::format('booleanVal', $rule,  $params);
        $this->assertSame(true, $rs);

        $rule = array('name' => 'dateVal', 'type' => 'date', 'format' => 'timestamp');
        $rs = PhalApi_Request_Var::format('dateVal', $rule,  $params);
        $this->assertSame( 1423065600, $rs);

        $rule = array('name' => 'arrayVal', 'type' => 'array', 'format' => 'explode');
        $rs = PhalApi_Request_Var::format('arrayVal', $rule,  $params);
        $this->assertSame(array('a', 'b', 'c'), $rs);

        $rule = array('name' => 'enumVal', 'type' => 'enum', 'range' => array('female', 'male'));
        $rs = PhalApi_Request_Var::format('enumVal', $rule,  $params);
        $this->assertSame('male', $rs);

        $rule = array('name' => 'noThisKey');
        $rs = PhalApi_Request_Var::format('noThisKey', $rule,  $params);
        $this->assertSame(null, $rs);

        $rule = array('name' => 'noThisKey', 'type' => 'noThisType');
        $rs = PhalApi_Request_Var::format('noThisKey', $rule,  $params);
        $this->assertSame(null, $rs);
    }

    /**
     * @expectedException PhalApi_Exception_InternalServerError
     */
    public function testGetEnumWithEmptyRange()
    {
        PhalApi_Request_Var::formatEnum('aHa~',  array('name' => 'key', 'type' => 'enum', 'range' => array()));
    }

    public function testStringWithRegxRight()
    {
        //very simple mobile phone
        $rule = array('name' => 'key', 'type' => 'string', 'regex' => '/^[0-9]{11}/');
        PhalApi_Request_Var::formatString('13800138000', $rule);
    }

    /**
     * @expectedException PhalApi_Exception_BadRequest
     */
    public function testStringWithRegxWrong()
    {
        $rule = array('name' => 'key', 'type' => 'string', 'regex' => '/^[0-9]{11}/');
        PhalApi_Request_Var::formatString('no a number', $rule);
    }
}
