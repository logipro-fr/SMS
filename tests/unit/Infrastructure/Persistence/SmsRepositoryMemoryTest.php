<?php

namespace Sms\tests\Infrastructure\Persistence;

use Sms\Infrastructure\Persistence\SmsRepositoryMemory;

class SmsRepositoryMemoryTest extends SmsRepositoryTestBase
{
    protected function initialize(): void
    {
        $this->repository = new SmsRepositoryMemory();
    }
}
