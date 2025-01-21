<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class MobilePhoneNumberTest extends TestCase
{
    public function testValidMobilePhoneNumber(): void
    {
        $phoneNumber = new MobilePhoneNumber('0612345678');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
        $this->assertEquals('+33612345678', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidMobilePhoneNumberWithCountryCode(): void
    {
        $phoneNumber = new MobilePhoneNumber('+33612345678');
        $this->assertEquals('+33612345678', $phoneNumber->getNumber());
        $this->assertEquals('+33612345678', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidMobilePhoneNumberWithSpaces(): void
    {
        $phoneNumber = new MobilePhoneNumber('06 12 34 56 78');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
        $this->assertEquals('+33612345678', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testInvalidMobilePhoneNumberWithNonMobilePrefix(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 0112345678');

        new MobilePhoneNumber('0112345678');
    }

    public function testInvalidMobilePhoneNumberWithLetters(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 06abc45678');

        new MobilePhoneNumber('06abc45678');
    }

    public function testEmptyMobilePhoneNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new MobilePhoneNumber('');
    }

    public function testInvalidMobilePhoneNumberFormat(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 061234567');

        new MobilePhoneNumber('061234567');
    }

    public function testInvalidMobilePhoneNumberWithInvalidCountryCode(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number +44612345678');

        new MobilePhoneNumber('+44612345678');
    }
}
