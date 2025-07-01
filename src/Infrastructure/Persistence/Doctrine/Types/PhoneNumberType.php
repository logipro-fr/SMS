<?php

namespace Sms\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\PhoneNumber;

class PhoneNumberType extends Type
{
    public const TYPE_NAME = 'phonenumber';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * @param MobilePhoneNumber $value
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
        return new MobilePhoneNumber($value);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'VARCHAR(255)';
    }
}
