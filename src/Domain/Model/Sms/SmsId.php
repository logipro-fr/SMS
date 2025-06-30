<?php

namespace Sms\Domain\Model\Sms;

class SmsId
{
    private string $id;

    public function __construct(?string $defaultId = null)
    {
        $this->id = $defaultId ?? 'sms_' . uniqid();
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function equals(SmsId $other): bool
    {
        return $this->id === (string) $other;
    }
}
