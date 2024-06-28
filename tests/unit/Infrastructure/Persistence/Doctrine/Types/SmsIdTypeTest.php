<?php

namespace Sms\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Doctrine\Types\SmsIdType;

class SmsIdTypeTest extends TestCase
{
    public function testGetNameTypes(): void
    {
        $this->assertEquals('sms_id', (new SmsIdType())->getName());
    }

    public function testConvertToPHPValue(): void
    {
        $type = new SmsIdType();
        $id = $type->convertToPHPValue("sms_id", new SqlitePlatform());
        $this->assertEquals(true, $id instanceof SmsId);
    }


    public function testConvertToDatabaseValue(): void
    {
        $type = new SmsIdType();
        $dbValue = $type->convertToDatabaseValue($id = new SmsId(), new SqlitePlatform());
        $this->assertEquals($id->__toString(), $dbValue);
    }
}
