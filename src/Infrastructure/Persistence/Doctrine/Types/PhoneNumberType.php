<?php

namespace Sms\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class PhoneNumberType extends Type
{
    public const TYPE_NAME = 'phonenumber';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }


    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return serialize($value);
    }

    /** @param string $value */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {

        $phonenumber = unserialize($value);

        return $phonenumber;
    }


    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {

        return $platform->getBlobTypeDeclarationSQL($column);
    }
}
