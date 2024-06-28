<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\SmsModel\SmsId;

class SendSmsResponse
{
    public function __construct(
        public readonly string $statusMessage,
        public readonly SmsId $smsId
    ) {
    }
}
