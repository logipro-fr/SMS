<?php

namespace Sms\Infrastructure\Persistence\Sms;

use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\SmsRepositoryInterface;

class SmsRepositoryMemory implements SmsRepositoryInterface
{
    /** @var array<string, Sms> */
    private array $smsList = [];

    public function add(Sms $sms): void
    {
        $smsIdString = (string) $sms->getId();
        $this->smsList[$smsIdString] = $sms;
    }

    public function findById(SmsId $smsId): Sms
    {
        $smsIdString = (string) $smsId;

        if (!isset($this->smsList[$smsIdString])) {
            throw new SmsNotFoundException("Error can't find the smsId '{$smsIdString}'", 400);
        }

        return $this->smsList[$smsIdString];
    }
}
