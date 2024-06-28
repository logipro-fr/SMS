<?php

namespace Sms\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Sms;
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
                new MobilePhoneNumber("+33623456789")
            ),
            new SqlitePlatform()
        );
        $this->assertIsString($dbValue);

        $phpValue = $type->convertToPHPValue($dbValue, new SqlitePlatform());
        $this->assertEquals($sms, $phpValue);
    }
}
