<?php

namespace Sms\Domain\Model\SmsModel;

class StatusMessage
{
    public function __construct(private string $statusMessage)
    {
    }
     /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function __toString(): string
    {
        return $this->statusMessage;
    }
}
