<?php
namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function testValidPhoneNumber()
    {
        $phoneNumber = new PhoneNumber('0123456789');
        $this->assertEquals('0123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidPhoneNumberWithCountryCode()
    {
        $phoneNumber = new PhoneNumber('+33123456789');
        $this->assertEquals('+33123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testValidPhoneNumberWithSpaces()
    {
        $phoneNumber = new PhoneNumber('01 23 45 67 89');
        $this->assertEquals('0123456789', $phoneNumber->getNumber());
        $this->assertEquals('+33123456789', $phoneNumber->getInternationalFormatedNumber());
    }

    public function testPhoneNumberWithLeadingAndTrailingSpaces()
    {
        $phoneNumber = new PhoneNumber(' 0612345678 ');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testInvalidPhoneNumberWithLetters()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 0123abc789');

        new PhoneNumber('0123abc789');
    }

    public function testPhoneNumberWithSpaceReplacement()
    {
        $phoneNumber = new PhoneNumber('06 12 34 56 78');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithLeadingSpaceReplacement()
    {
        $phoneNumber = new PhoneNumber(' 0612345678');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithTrailingSpaceReplacement()
    {
        $phoneNumber = new PhoneNumber('0612345678 ');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testPhoneNumberWithMultipleSpacesReplacement()
    {
        $phoneNumber = new PhoneNumber('06  12  34  56  78');
        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testEmptyPhoneNumber()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new PhoneNumber('');
    }

    public function testInvalidPhoneNumberFormat()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 01234567');

        new PhoneNumber('01234567');
    }

    public function testInvalidPhoneNumberWithInvalidCountryCode()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number +44123456789');

        new PhoneNumber('+44123456789');
    }

    public function testPhoneNumberWithOnlySpaces()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new PhoneNumber(' ');
    }

    public function testPhoneNumberWithMixedSpacesAndInvalidCharacters()
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Invalid phone number 0612abc678');

        new PhoneNumber('06 12 abc 678');
    }
}
