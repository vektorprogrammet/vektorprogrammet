<?php

namespace AppBundle\Tests\Sms;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SenderTest extends KernelTestCase
{
    private $sender;
    
    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->sender = $kernel->getContainer()->get('app.sms');
    }

    public function testNumberCleanerValid8Digit()
    {
        $expected = '4712345678';
        $actual = $this->sender->cleanPhoneNumber('12345678');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerNumberWithSpaces()
    {
        $expected = '4712345678';
        $actual = $this->sender->cleanPhoneNumber('123 45 678');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerAlreadyClean()
    {
        $expected = '4712345678';
        $actual = $this->sender->cleanPhoneNumber('4712345678');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerStartWithPlus()
    {
        $expected = '4712345678';
        $actual = $this->sender->cleanPhoneNumber('+4712345678');

        self::assertEquals($expected, $actual);
    }
    
    public function testNumberCleanerEmptyString()
    {
        $expected = false;
        $actual = $this->sender->cleanPhoneNumber('');
        
        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooShort()
    {
        $expected = false;
        $actual = $this->sender->cleanPhoneNumber('1234567');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLong()
    {
        $expected = false;
        $actual = $this->sender->cleanPhoneNumber('123456789');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLongStartWithLandCode()
    {
        $expected = false;
        $actual = $this->sender->cleanPhoneNumber('47123456789');

        self::assertEquals($expected, $actual);
    }

    public function testNumberCleanerTooLongStartWithPlus()
    {
        $expected = false;
        $actual = $this->sender->cleanPhoneNumber('+47123456789');

        self::assertEquals($expected, $actual);
    }
}
