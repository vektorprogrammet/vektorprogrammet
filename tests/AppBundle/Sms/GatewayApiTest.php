<?php

namespace Tests\AppBundle\Sms;

use AppBundle\Service\LogService;
use AppBundle\Sms\GatewayAPI;
use AppBundle\Sms\Sms;
use PHPUnit\Framework\TestCase;

class GatewayApiTest extends TestCase
{
    /**
     * @var GatewayAPI
     */
    private $gatewayApi;

    protected function setUp()
    {
        $loggerMock = $this->createMock(LogService::class);
        $this->gatewayApi = new GatewayAPI([
        	'disable_delivery' => true,
	        'max_length' => 2000,
	        'api_token' => "SECRET",
	        'country_code' => '47'
        ], $loggerMock);
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

    public function testValidateNumberTooShortAndStartsWithCountryCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('471234567');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooShortAndStartsWithPlusCountryCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('+471234567');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLong()
    {
        $expected = false;
        $actual = $this->gatewayApi->validatePhoneNumber('123456789');

        self::assertEquals($expected, $actual);
    }

    public function testValidateNumberTooLongAndStartsWithCountryCode()
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

    public function testFormatNumberTooShortAndStartsWithCountryCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('471234567');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooShortAndStartsWithPlusCountryCode()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('+471234567');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooLong()
    {
        $expected = false;
        $actual = $this->gatewayApi->formatPhoneNumber('123456789');

        self::assertEquals($expected, $actual);
    }

    public function testFormatNumberTooLongStartWithCountryCode()
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

    public function testSendWithoutCrashing()
    {
        $sms = new Sms();
        $sms->setRecipients(['12345678']);
        $sms->setSender('Vektorbot');
        $sms->setMessage('This is a test message');

        $this->gatewayApi->send($sms);

        self::assertTrue(true); // Test will be marked as "risky" if there are no asserts
    }
}
