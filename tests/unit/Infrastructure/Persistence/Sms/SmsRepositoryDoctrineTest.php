<?php

namespace Sms\Tests\Infrastructure\Persistence\Sms;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use DoctrineTestingTools\DoctrineRepositoryTesterTrait;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;
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

    public function testAddPersistsAndFlushes(): void
    {
        $sms = $this->createMock(Sms::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist')->with($sms);
        $em->expects($this->once())->method('flush');

        $classMetadata = new ClassMetadata(Sms::class);
        $em->method('getClassMetadata')->willReturn($classMetadata);

        $repo = new SmsRepositoryDoctrine($em);
        $repo->add($sms);
    }


    public function testFindByIdThrowsExceptionIfNotFound(): void
    {
        $smsId = $this->createMock(SmsId::class);
        $smsId->method('__toString')->willReturn('12345');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('find')->willReturn(null);

        $classMetadata = new ClassMetadata(Sms::class);
        $classMetadata->name = Sms::class; // <- essentiel

        $em->method('getClassMetadata')->willReturn($classMetadata);

        $repo = new SmsRepositoryDoctrine($em);

        $this->expectException(SmsNotFoundException::class);
        $repo->findById($smsId);
    }
}
