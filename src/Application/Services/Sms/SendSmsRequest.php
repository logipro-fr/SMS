<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\PhoneNumber;

class SendSmsRequest
{
    public function __construct(
        public readonly PhoneNumber $phoneNumber,
        public readonly MessageText $message
    ) {
    }
}
