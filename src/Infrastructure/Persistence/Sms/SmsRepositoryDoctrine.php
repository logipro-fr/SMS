<?php

namespace Sms\Infrastructure\Persistence\Sms;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\SmsRepositoryInterface;
use Sms\Infrastructure\Persistence\Sms\SmsNotFoundException;

/** @extends EntityRepository<Sms> */
class SmsRepositoryDoctrine extends EntityRepository implements SmsRepositoryInterface
{
    public function __construct(EntityManagerInterface $em)
    {
        $classMetadata = $em->getClassMetadata(Sms::class);
        parent::__construct($em, $classMetadata);
    }

    public function add(Sms $sms): void
    {
        $em = $this->getEntityManager();
        $em->persist($sms);
        $em->flush();
    }

    public function findById(SmsId $smsId): Sms
    {
        $sms = $this->getEntityManager()->find(Sms::class, $smsId);

        if ($sms === null) {
            throw new SmsNotFoundException("Error can't find the smsId '{$smsId}'");
        }

        return $sms;
    }
}
