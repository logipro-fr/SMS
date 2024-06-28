<?php

namespace Sms\Tests\Infrastructure\Persistence\Sms;

use DoctrineTestingTools\DoctrineRepositoryTesterTrait;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryDoctrine;

class SmsRepositoryDoctrineTest extends SmsRepositoryTestBase
{
    use DoctrineRepositoryTesterTrait;

    protected function initialize(): void
    {
        $this->initDoctrineTester();
        $this->clearTables(["sms"]);
        $this->repository = new SmsRepositoryDoctrine($this->getEntityManager());
    }
}
