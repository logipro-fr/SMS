<?php

namespace Sms\Domain\Model\SmsModel;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\SmsModel\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    private const PHONE_NUMBER = '+33123456789';

    public function testPhoneNumber(): void
    {
        $phoneNumber = new PhoneNumber([self::PHONE_NUMBER]);
        $this->assertEquals([self::PHONE_NUMBER], $phoneNumber->getPhoneNumber());
        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
    }

    public function testToString(): void
    {
        $phoneNumber = new PhoneNumber([self::PHONE_NUMBER]);
        $this->assertNotEmpty($phoneNumber->__ToString());
        $this->assertIsString($phoneNumber->__ToString());
    }
}
