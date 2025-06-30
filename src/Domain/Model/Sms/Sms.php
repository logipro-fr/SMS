<?php

namespace Sms\Domain\Model\Sms;

use DateTimeImmutable;

class Sms
{
    private SmsId $id;
    private MessageText $messageText;
    private PhoneNumber $phoneNumber;
    private DateTimeImmutable $createdAt;

    public function __construct(
        MessageText $messageText,
        PhoneNumber $phoneNumber,
        SmsId $id,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->messageText = $messageText;
        $this->phoneNumber = $phoneNumber;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): SmsId
    {
        return $this->id;
    }

    public function getSmsMessage(): string
    {
        return $this->messageText->getMessageText();
    }

    public function getSmsPhoneNumber(): string
    {
        return $this->phoneNumber->getNumber();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
