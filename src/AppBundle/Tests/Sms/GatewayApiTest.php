<?php

namespace AppBundle\Tests\Sms;

use AppBundle\Sms\GatewayAPI;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GatewayApiTest extends KernelTestCase
{
    private $landCode = '47';
    /**
     * @var GatewayAPI
     */
    private $gatewayApi;

    protected function setUp()
    {
        parent::setUp();

        $kernel = $this->createKernel();
        $kernel->boot();
        $this->gatewayApi = $kernel->getContainer()->get('app.sms');
    }

    public function testValidateEightDigitNumber()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('12345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithSpaces()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('123 45 678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithCountryCode()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('4712345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithPlusAndCountryCode()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('+4712345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }
    
    public function testValidateEmptyString()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('', $this->landCode);
        
        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooShort()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('1234567', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLong()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLongAndStartsWithLandCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('47123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLongAndStartWithPlus()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('+47123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }
}
