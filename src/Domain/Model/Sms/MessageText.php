<?php

namespace Sms\Domain\Model\Sms;

class MessageText
{
    public function __construct(private string $messageText)
    {
    }
    public function getMessageText(): string
    {
        return $this->messageText;
    }

    public function __toString()
    {
        return strval($this->messageText);
    }
}
