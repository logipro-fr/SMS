<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function testValidPhoneNumber(): void
    {
        $phoneNumber = new PhoneNumber('0123456789');
        $this->assertEquals('0123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidPhoneNumberWithCountryCode(): void
    {
        $phoneNumber = new PhoneNumber('+33123456789');
        $this->assertEquals('+33123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidPhoneNumberWithSpaces(): void
    {
        $phoneNumber = new PhoneNumber('01 23 45 67 89');
        $this->assertEquals('0123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testPhoneNumberWithLeadingAndTrailingSpaces(): void
    {
        $phoneNumber = new PhoneNumber(' 0612345678 ');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testInvalidPhoneNumberWithLetters(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 0123abc789');

        new PhoneNumber('0123abc789');
    }

    public function testPhoneNumberWithSpaceReplacement(): void
    {
        $phoneNumber = new PhoneNumber('06 12 34 56 78');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithLeadingSpaceReplacement(): void
    {
        $phoneNumber = new PhoneNumber(' 0612345678');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithTrailingSpaceReplacement(): void
    {
        $phoneNumber = new PhoneNumber('0612345678 ');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithMultipleSpacesReplacement(): void
    {
        $phoneNumber = new PhoneNumber('06  12  34  56  78');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testEmptyPhoneNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new PhoneNumber('');
    }

    public function testInvalidPhoneNumberFormat(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 01234567');

        new PhoneNumber('01234567');
    }

    public function testInvalidPhoneNumberWithInvalidCountryCode(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number +44123456789');

        new PhoneNumber('+44123456789');
    }

    public function testPhoneNumberWithOnlySpaces(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new PhoneNumber(' ');
    }

    public function testPhoneNumberWithMixedSpacesAndInvalidCharacters(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 0612abc678');

        new PhoneNumber('06 12 abc 678');
    }
}
