<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;

class SendSmsRequest implements RequestInterface
{
    public function __construct(
        public readonly PhoneNumber $phoneNumber,
        public readonly MessageText $message
    ) {
    }
}
