<?php

namespace Sms\Infrastructure\SmsProvider;

use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\StatusMessage;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;

interface SenderProviderInterface
{
    public function sendSms(PhoneNumber $phoneNumber, MessageText $message): StatusMessage;
}
