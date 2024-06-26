<?php

namespace Sms\Infrastructure\Persistence;

use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Domain\Model\SmsModel\SmsRepositoryInterface;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;

class SmsRepositoryMemory implements SmsRepositoryInterface
{
    /** @var array<Sms> $smsList */
    private array $smsList = [];
    public function add(Sms $sms): void
    {
        $this->smsList[$sms->getId()->__toString()] = $sms;
    }

    public function findById(SmsId $smsId): Sms
    {
        $smsIdString = $smsId->__toString();
        if (!isset($this->smsList[$smsIdString])) {
            throw new SmsNotFoundException("Error can't find the smsId", 400);
        }
        return $this->smsList[$smsId->__toString()];
    }
}
