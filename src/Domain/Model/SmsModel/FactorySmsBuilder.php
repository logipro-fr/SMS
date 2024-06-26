<?php

namespace Sms\Domain\Model\SmsModel;

use Sms\Application\Services\Sms\RequestServiceSms;

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
        SmsId $smsId
    ): RequestServiceSms {
        $sms = self::createSms($smsMessage, $smsPhoneNumber, $smsId);
        return new RequestServiceSms($sms);
    }
}
