<?php

namespace Sms\Domain\Model\Sms;

use Safe\DateTimeImmutable;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\PhoneNumber;

class Sms
{
    public DateTimeImmutable $createdAt;
    public function __construct(
        private MessageText $messagetext,
        private PhoneNumber $phoneNumber,
        private SmsId $id = new SmsId()
    ) {
        $this->messagetext = $messagetext;
        $this->phoneNumber = $phoneNumber;
        $this->createdAt = new DateTimeImmutable();
    }


    public function getSmsMessage(): string
    {
        return ($this->messagetext->getMessagetext());
    }

    /** @return array<string> */
    public function getSmsPhoneNumber(): array
    {
        return ($this->phoneNumber->getPhoneNumber());
    }


    public function getId(): SmsId
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
