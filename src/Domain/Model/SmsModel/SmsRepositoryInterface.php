<?php

namespace Sms\Domain\Model\SmsModel;

use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsId;

interface SmsRepositoryInterface
{
    public function add(Sms $sms): void;
    public function findById(SmsId $smsId): Sms;
}
