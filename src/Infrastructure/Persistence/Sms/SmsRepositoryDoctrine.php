<?php

namespace Sms\Infrastructure\Persistence\Sms;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Domain\Model\SmsModel\SmsRepositoryInterface;

/** @extends EntityRepository<Sms>*/

class SmsRepositoryDoctrine extends EntityRepository implements SmsRepositoryInterface
{
    public function __construct(EntityManagerInterface $em)
    {
        $class = $em->getClassMetadata(Sms::class);
        parent::__construct($em, $class);
    }

    public function add(Sms $sms): void
    {
        $this->getEntityManager()->persist($sms);
    }

    public function findById(SmsId $smsId): Sms
    {
        $sms = $this->getEntityManager()->find(Sms::class, $smsId);
        if ($sms === null) {
            throw new SmsNotFoundException("Error can't find the smsId", 400);
        }
        return $sms;
    }
}
