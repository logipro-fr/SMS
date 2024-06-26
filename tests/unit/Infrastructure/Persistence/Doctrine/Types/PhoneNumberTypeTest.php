<?php

namespace Sms\tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
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
        $dbValue = $type->convertToDatabaseValue(
            $sms = new Sms(
                new MessageText("ceci est un message !"),
                new PhoneNumber(["+33123456789"])
            ),
            new SqlitePlatform()
        );
        $this->assertIsString($dbValue);

        $phpValue = $type->convertToPHPValue($dbValue, new SqlitePlatform());
        $this->assertEquals($sms, $phpValue);
    }
}
