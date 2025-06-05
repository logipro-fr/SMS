<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class PhoneNumberTest extends TestCase
{
    private const LOCAL_NUMBER = '0123456789';
    private const FORMATTED_WITH_SPACES = '01 23 45 67 89';
    private const WITH_LETTERS = '0123abc789';
    private const INTERNATIONAL_NUMBER = '+33123456789';
    private const INVALID_COUNTRY_CODE = '+44123456789';
    private const INCOMPLETE_NUMBER = '01234567';
    private const ONLY_SPACES = ' ';
    private const MIXED_SPACES_AND_LETTERS = '06 12 abc 678';
    private const ERROR_INVALID = 'Invalid phone number ';
    private const ERROR_EMPTY = 'Bad and empty character in phone number';

    public function testShouldAcceptValidLocalNumber(): void
    {
        $phoneNumber = new PhoneNumber(self::LOCAL_NUMBER);

        $this->assertEquals(self::LOCAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldAcceptValidInternationalNumber(): void
    {
        $phoneNumber = new PhoneNumber(self::INTERNATIONAL_NUMBER);

        $this->assertEquals(self::INTERNATIONAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldFormatNumberWithSpaces(): void
    {
        $phoneNumber = new PhoneNumber(self::FORMATTED_WITH_SPACES);

        $this->assertEquals(self::LOCAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldTrimLeadingAndTrailingSpaces(): void
    {
        $phoneNumber = new PhoneNumber(' ' . self::LOCAL_NUMBER . ' ');

        $this->assertEquals(self::LOCAL_NUMBER, $phoneNumber->getNumber());
    }

    public function testShouldRemoveExtraSpacesBetweenDigits(): void
    {
        $phoneNumber = new PhoneNumber('06  12  34  56  78');

        $this->assertEquals('0612345678', $phoneNumber->getNumber());
    }

    public function testShouldThrowForLettersInPhoneNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_INVALID . self::WITH_LETTERS);

        new PhoneNumber(self::WITH_LETTERS);
    }

    public function testShouldThrowForEmptyPhoneNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_EMPTY);

        new PhoneNumber('');
    }

    public function testShouldThrowForIncompletePhoneNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_INVALID . self::INCOMPLETE_NUMBER);

        new PhoneNumber(self::INCOMPLETE_NUMBER);
    }

    public function testShouldThrowForInvalidCountryCode(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_INVALID . self::INVALID_COUNTRY_CODE);

        new PhoneNumber(self::INVALID_COUNTRY_CODE);
    }

    public function testShouldThrowForOnlySpaces(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_EMPTY);

        new PhoneNumber(self::ONLY_SPACES);
    }

    public function testShouldThrowForMixedSpacesAndInvalidCharacters(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::ERROR_INVALID . '0612abc678');

        new PhoneNumber(self::MIXED_SPACES_AND_LETTERS);
    }
}
