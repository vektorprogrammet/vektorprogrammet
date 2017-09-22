<?php

namespace AppBundle\Tests\Sms;

use AppBundle\Sms\GatewayAPI;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GatewayApiTest extends KernelTestCase
{
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
        $actual = $this->gatewayApi->validatePhoneNumber('12345678');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithSpaces()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('123 45 678');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithCountryCode()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('4712345678');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberWithPlusAndCountryCode()
    {
        $expected = true;
        $actual = $this->gatewayApi->validatePhoneNumber('+4712345678');

        self::assertEquals($expected, $actual);
    }
    
    public function testValidateEmptyString()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('');
        
        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooShort()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('1234567');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLong()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('123456789');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLongAndStartsWithLandCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('47123456789');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLongAndStartWithPlus()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('+47123456789');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberEightDigits()
    {
        $expected = '4712345678';
        $actual = $this->gatewayApi->formatPhoneNumber('12345678');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberWithSpaces()
    {
        $expected = '4712345678';
        $actual = $this->gatewayApi->formatPhoneNumber('123 45 678');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberAlreadyFormatted()
    {
        $expected = '4712345678';
        $actual = $this->gatewayApi->formatPhoneNumber('4712345678');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberStartWithPlus()
    {
        $expected = '4712345678';
        $actual = $this->gatewayApi->formatPhoneNumber('+4712345678');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberEmptyString()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooShort()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('1234567');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooLong()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('123456789');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooLongStartWithLandCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('47123456789');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooLongStartWithPlus()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('+47123456789');

        self::assertEquals($expected, $actual);
    }
}
