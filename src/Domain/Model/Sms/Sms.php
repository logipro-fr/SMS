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
        private MobilePhoneNumber $phoneNumber,
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

    public function getSmsPhoneNumber(): string
    {
        return $this->phoneNumber->getNumber();
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
