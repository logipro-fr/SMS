<?php

namespace Sms\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Infrastructure\Persistence\Doctrine\Types\MessageTextType;

class MessageTextTypeTest extends TestCase
{
    public function testGetNameTypes(): void
    {
        $this->assertEquals('messagetext', (new MessageTextType())->getName());
    }

    public function testConvertToPHPValue(): void
    {
        $type = new MessageTextType();
        $id = $type->convertToPHPValue("messagetext", new SqlitePlatform());
        $this->assertEquals(true, $id instanceof MessageText);
    }


    public function testConvertToDatabaseValue(): void
    {
        $type = new MessageTextType();
        $dbValue = $type->convertToDatabaseValue($id = new MessageText("ceci est un message !"), new SqlitePlatform());
        $this->assertEquals($id->__toString(), $dbValue);
    }
}
