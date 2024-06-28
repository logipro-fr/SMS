<?php

namespace Sms\Domain\Model\Sms;

class MessageText
{
    public function __construct(private string $messagetext)
    {
    }
    public function getMessagetext(): string
    {
        return $this->messagetext;
    }

    public function __toString()
    {
        return strval($this->messagetext);
    }
}
