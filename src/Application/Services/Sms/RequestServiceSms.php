<?php

namespace Sms\Application\Services\Sms;

use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;

class RequestServiceSms implements RequestInterface
{
    public function __construct(public readonly Sms $sms)
    {
    }
}
