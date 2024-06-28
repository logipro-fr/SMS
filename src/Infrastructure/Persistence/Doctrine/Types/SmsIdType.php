<?php

namespace Sms\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Sms\Domain\Model\SmsModel\SmsId;

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
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->getId();
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
     * @return string
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
