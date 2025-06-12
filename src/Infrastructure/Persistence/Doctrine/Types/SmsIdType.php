<?php

namespace Sms\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Sms\Domain\Model\Sms\SmsId;
use InvalidArgumentException;

class SmsIdType extends Type
{
    public const TYPE_NAME = 'sms_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
    /**
     * @param SmsId $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return (string) $value;
    }
    /**
     * @param string $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): SmsId
    {
                return new SmsId($value);
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
