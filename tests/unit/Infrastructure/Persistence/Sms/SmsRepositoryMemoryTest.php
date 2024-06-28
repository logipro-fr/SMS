<?php

namespace Sms\Tests\Infrastructure\Persistence\Sms;

use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;

class SmsRepositoryMemoryTest extends SmsRepositoryTestBase
{
    protected function initialize(): void
    {
        $this->repository = new SmsRepositoryMemory();
    }
}
