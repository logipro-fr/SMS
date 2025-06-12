<?php

namespace Sms\Tests\Infrastructure\Persistence\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\SmsRepositoryInterface;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;

abstract class SmsRepositoryTestBase extends TestCase
{
    protected SmsRepositoryInterface $repository;

    private const PHONE_NUMBRE_INTERNATIONAL = "+33623456789";

    protected function setUp(): void
    {
        $this->initialize();
    }

    abstract protected function initialize(): void;

    public function testFindById(): void
    {
        $id = new SmsId("id");
        $id2 = new SmsId("id2");

        $sms = new Sms(new MessageText("test"), new MobilePhoneNumber(self::PHONE_NUMBRE_INTERNATIONAL), $id);
        $sms2 = new Sms(new MessageText("test2"), new MobilePhoneNumber(self::PHONE_NUMBRE_INTERNATIONAL), $id2);

        $this->repository->add($sms);
        $found = $this->repository->findById($id);
        $this->repository->add($sms2);
        $found2 = $this->repository->findById($id2);
        $idFound = $found->getId();

        $this->assertEquals("id2", $found2->getId()->__toString());
        $this->assertInstanceOf(Sms::class, $found);
        $this->assertFalse($idFound->equals($found2->getId()));
    }
}
