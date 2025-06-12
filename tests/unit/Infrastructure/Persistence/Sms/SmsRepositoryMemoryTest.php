<?php

namespace Sms\Tests\Infrastructure\Persistence\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;

class SmsRepositoryMemoryTest extends TestCase
{
    private SmsRepositoryMemory $repository;
    private const TEST_PHONE = '+33612345678';

    protected function setUp(): void
    {
        $this->repository = new SmsRepositoryMemory();
    }

    public function testAddAndFindById(): void
    {
        $smsId = new SmsId('abc123');
        $sms = new Sms(new MessageText('Hello'), new MobilePhoneNumber(self::TEST_PHONE), $smsId);

        $this->repository->add($sms);
        $found = $this->repository->findById($smsId);

        $this->assertSame($sms, $found);
    }

    public function testFindByIdThrowsException(): void
    {
        $this->expectException(SmsNotFoundException::class);
        $this->expectExceptionMessage("Error can't find the smsId");

        $this->repository->findById(new SmsId('not_existing_id'));
    }

    public function testAddAndFindByIdReturnsSms(): void
    {
        $smsId = $this->createMock(SmsId::class);
        $smsId->method('__toString')->willReturn('abc123');

        $sms = $this->createMock(Sms::class);
        $sms->method('getId')->willReturn($smsId);

        $repo = new SmsRepositoryMemory();
        $repo->add($sms);

        $found = $repo->findById($smsId);
        $this->assertSame($sms, $found);
    }

    public function testFindByIdThrowsIfNotFound(): void
    {
        $smsId = $this->createMock(SmsId::class);
        $smsId->method('__toString')->willReturn('non-existent-id');

        $repo = new SmsRepositoryMemory();

        $this->expectException(SmsNotFoundException::class);
        $this->expectExceptionMessage("Error can't find the smsId");
        $this->expectExceptionCode(400);

        $repo->findById($smsId);
    }
}
