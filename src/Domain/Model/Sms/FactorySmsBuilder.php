<?php

namespace Sms\Domain\Model\Sms;

use Sms\Application\Services\Sms\SendSmsRequest;

class FactorySmsBuilder
{
    public function createSms(
        MessageText $messageText,
        MobilePhoneNumber $phoneNumber,
        ?SmsId $smsId = null
    ): Sms {
        $smsId = $smsId ?? new SmsId();
        return new Sms($messageText, $phoneNumber, $smsId);
    }

    public function createRequestServiceSms(
        MessageText $messageText,
        MobilePhoneNumber $phoneNumber
    ): SendSmsRequest {
        return new SendSmsRequest($phoneNumber, $messageText);
    }
}
