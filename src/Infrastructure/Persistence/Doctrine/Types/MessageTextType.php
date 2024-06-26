<?php

namespace Sms\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Sms\Domain\Model\SmsModel\MessageText;

class MessageTextType extends Type
{
    public const TYPE_NAME = 'messagetext';
    public function getName()
    {
        return self::TYPE_NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return "text";
    }
    /**
     * @param MessageText $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->__toString();
    }
    /**
     * @param string $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var MessageText */
        return new MessageText($value);
    }
}
