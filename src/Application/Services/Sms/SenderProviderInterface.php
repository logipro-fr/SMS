<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\StatusMessage;

interface SenderProviderInterface
{
    public function sendSmsService(MobilePhoneNumber $phoneNumber, MessageText $message): StatusMessage;
}
