<?php

namespace AppBundle\Tests\Sms;

use AppBundle\Sms\PhoneNumberFormatter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhoneNumberFormatterTest extends KernelTestCase
{
    private $landCode = '47';

    public function testNumberCleanerValid8Digit()
    {
        $expected = '4712345678';
        $actual = PhoneNumberFormatter::format('12345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerNumberWithSpaces()
    {
        $expected = '4712345678';
        $actual = PhoneNumberFormatter::format('123 45 678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerAlreadyClean()
    {
        $expected = '4712345678';
        $actual = PhoneNumberFormatter::format('4712345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerStartWithPlus()
    {
        $expected = '4712345678';
        $actual = PhoneNumberFormatter::format('+4712345678', $this->landCode);

        self::assertEquals($expected, $actual);
    }
    
    public function testNumberCleanerEmptyString()
    {
        $expected = false;
        $actual = PhoneNumberFormatter::format('', $this->landCode);
        
        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooShort()
    {
        $expected = false;
        $actual = PhoneNumberFormatter::format('1234567', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLong()
    {
        $expected = false;
        $actual = PhoneNumberFormatter::format('123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLongStartWithLandCode()
    {
        $expected = false;
        $actual = PhoneNumberFormatter::format('47123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLongStartWithPlus()
    {
        $expected = false;
        $actual = PhoneNumberFormatter::format('+47123456789', $this->landCode);

        self::assertEquals($expected, $actual);
    }
}
