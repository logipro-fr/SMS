<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;

class SendSmsRequest
{
    public function __construct(
        public readonly MobilePhoneNumber $phoneNumber,
        public readonly MessageText $message
    ) {
    }
}
