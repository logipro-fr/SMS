<?php

namespace Sms\Infrastructure\SmsProvider\Ovh;

use Sms\Application\Services\Sms\RequestInterface;
use Sms\Domain\Model\SmsModel\Sms;

class RequestSms implements RequestInterface
{
    public function __construct(public readonly Sms $sms)
    {
    }
}
