<?php

namespace Sms\Domain\Model\Sms;

use Sms\Application\Services\Sms\SendSmsRequest;

class FactorySmsBuilder
{
    public static function createSms(
        string $smsMessage,
        string $smsPhoneNumber,
        SmsId $smsId = new SmsId()
    ): Sms {
        $messageText = new MessageText($smsMessage);
        $phoneNumber = new MobilePhoneNumber($smsPhoneNumber);

        return new Sms($messageText, $phoneNumber, $smsId);
    }

    public static function createRequestServiceSms(
        string $smsMessage,
        string $smsPhoneNumber,
    ): SendSmsRequest {
        return new SendSmsRequest(new MobilePhoneNumber($smsPhoneNumber), new MessageText($smsMessage));
    }
}
