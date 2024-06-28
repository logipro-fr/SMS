<?php

namespace Sms\Domain\Model\Sms;

use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;

interface SmsRepositoryInterface
{
    public function add(Sms $sms): void;
    public function findById(SmsId $smsId): Sms;
}
