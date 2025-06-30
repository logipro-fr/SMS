<?php

namespace Sms\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Doctrine\Types\PhoneNumberType;

class PhoneNUmberTypeTest extends TestCase
{
    public function testGetNameTypes(): void
    {
        $this->assertEquals('phonenumber', (new PhoneNumberType())->getName());
    }

    public function testConvertValuePhoneNUmberType(): void
    {
        $type = new PhoneNumberType();
        $phone = new MobilePhoneNumber("+33623456789");

        $dbValue = $type->convertToDatabaseValue($phone, new SqlitePlatform());
        $this->assertIsString($dbValue);
        $this->assertEquals("+33623456789", $dbValue);

        $phpValue = $type->convertToPHPValue($dbValue, new SqlitePlatform());
        $this->assertEquals($phone, $phpValue);
    }
}
