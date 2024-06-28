<?php

namespace Sms\Domain\Model\SmsModel;

class SmsId
{
    private string $id;

    public function __construct(string $id = "")
    {
        $this->id = $id ?: uniqid("sms_");
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function equals(SmsId $smsId): bool
    {
        return $this->id === $smsId->id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
