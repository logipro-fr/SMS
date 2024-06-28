<?php

namespace Sms\Domain\Model\Sms;

use Sms\Application\Services\Sms\SendSmsRequest;

class FactorySmsBuilder
{
    /** @param array<string> $smsPhoneNumber */
    public static function createSms(
        string $smsMessage,
        array $smsPhoneNumber,
        SmsId $smsId = new SmsId()
    ): Sms {
        $messageText = new MessageText($smsMessage);
        $phoneNumber = new PhoneNumber($smsPhoneNumber);

        return new Sms($messageText, $phoneNumber, $smsId);
    }

    /** @param array<string> $smsPhoneNumber */
    public static function createRequestServiceSms(
        string $smsMessage,
        array $smsPhoneNumber,
    ): SendSmsRequest {
        return new SendSmsRequest(new PhoneNumber($smsPhoneNumber), new MessageText($smsMessage));
    }
}
