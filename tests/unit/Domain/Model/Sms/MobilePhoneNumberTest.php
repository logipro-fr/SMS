<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class MobilePhoneNumberTest extends TestCase
{
    private const LOCAL_NUMBER = '0612345678';
    private const FORMATTED_WITH_SPACES = '06 12 34 56 78';
    private const VALID_INTERNATIONAL_NUMBER = '+33612345678';
    private const INVALID_NON_MOBILE = '0112345678';
    private const INVALID_WITH_LETTERS = '06abc45678';
    private const INVALID_COUNTRY_CODE = '+44612345678';
    private const INCOMPLETE_NUMBER = '061234567';
    private const INVALID_ERROR = 'Invalid phone number ';

    public function testShouldAcceptValidLocalNumber(): void
    {
        $phoneNumber = new MobilePhoneNumber(self::LOCAL_NUMBER);

        $this->assertEquals(self::LOCAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::VALID_INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldAcceptValidInternationalNumber(): void
    {
        $phoneNumber = new MobilePhoneNumber(self::VALID_INTERNATIONAL_NUMBER);

        $this->assertEquals(self::VALID_INTERNATIONAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::VALID_INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldFormatNumberWithSpaces(): void
    {
        $phoneNumber = new MobilePhoneNumber(self::FORMATTED_WITH_SPACES);

        $this->assertEquals(self::LOCAL_NUMBER, $phoneNumber->getNumber());
        $this->assertEquals(self::VALID_INTERNATIONAL_NUMBER, $phoneNumber->getInternationalFormatedNumber());
    }

    public function testShouldThrowExceptionForNonMobilePrefix(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::INVALID_ERROR . self::INVALID_NON_MOBILE);

        new MobilePhoneNumber(self::INVALID_NON_MOBILE);
    }

    public function testShouldThrowExceptionForNumberWithLetters(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::INVALID_ERROR . self::INVALID_WITH_LETTERS);

        new MobilePhoneNumber(self::INVALID_WITH_LETTERS);
    }

    public function testShouldThrowExceptionForEmptyNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage('Bad and empty character in phone number');

        new MobilePhoneNumber('');
    }

    public function testShouldThrowExceptionForIncompleteNumber(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::INVALID_ERROR  . self::INCOMPLETE_NUMBER);

        new MobilePhoneNumber(self::INCOMPLETE_NUMBER);
    }

    public function testShouldThrowExceptionForInvalidCountryCode(): void
    {
        $this->expectException(InvalidPhoneNumber::class);
        $this->expectExceptionMessage(self::INVALID_ERROR . self::INVALID_COUNTRY_CODE);

        new MobilePhoneNumber(self::INVALID_COUNTRY_CODE);
    }
}
