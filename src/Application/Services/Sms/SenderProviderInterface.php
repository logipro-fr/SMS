<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\StatusMessage;

interface SenderProviderInterface
{
    public function sendSms(PhoneNumber $phoneNumber, MessageText $message): StatusMessage;
}
