<?php

namespace Sms\tests\Infrastructure\Persistence;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Domain\Model\SmsModel\SmsRepositoryInterface;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;

abstract class SmsRepositoryTestBase extends TestCase
{
    protected SmsRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->initialize();
    }

    abstract protected function initialize(): void;

    public function testFindById(): void
    {
        $id = new SmsId("id");
        $id2 = new SmsId("id2");

        $sms = new Sms(new MessageText("test"), new PhoneNumber(["+33123456789"]), $id);
        $sms2 = new Sms(new MessageText("test2"), new PhoneNumber(["+33123456789"]), $id2);

        $this->repository->add($sms);
        $found = $this->repository->findById($id);
        $this->repository->add($sms2);
        $found2 = $this->repository->findById($id2);
        $idFound = $found->getId();

        $this->assertEquals("id2", $found2->getId()->__toString());
        $this->assertInstanceOf(Sms::class, $found);
        $this->assertFalse($idFound->equals($found2->getId()));
    }

    public function testFindByIdException(): void
    {
        $id = new SmsId("id");
        $sms = new Sms(new MessageText("test"), new PhoneNumber(["+33123456789"]), $id);
        $this->repository->add($sms);

        $this->expectException(SmsNotFoundException::class);
        $this->expectExceptionMessage("Error can't find the smsId");
        $this->expectExceptionCode(400);

        $this->repository->findById(new SmsId("prime54845"));
    }
}
