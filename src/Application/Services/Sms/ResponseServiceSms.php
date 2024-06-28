<?php

namespace Sms\Application\Services\Sms;

class ResponseServiceSms
{
    public function __construct(public readonly string $statusMessage)
    {
    }
}
