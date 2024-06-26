<?php

namespace Sms\Application\Services\Sms;

use Sms\Infrastructure\SmsProvider\Ovh\SendSms;
use Sms\Application\Services\Sms\RequestServiceSms;
use Sms\Application\Services\Sms\ResponseServiceSms;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\StatusMessage;
use Sms\Infrastructure\Persistence\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;

class SmsService
{
    public SmsRepositoryMemory $repository;
    public ResponseServiceSms $response;

    public function __construct(SmsRepositoryMemory $repository, private SendSms $sendSms)
    {
        $this->repository = $repository;
        $this->sendSms = $sendSms;
    }

    public function execute(RequestServiceSms $requestSms): void
    {
        $statusMessageObject = $this->sendSms->sendSms(new RequestSms($requestSms->sms));
        $statusMessageText = $statusMessageObject->getStatusMessage();

        $statusMessage = new StatusMessage($statusMessageText);

        $sms = new Sms(
            new MessageText($requestSms->sms->getSmsMessage()),
            new PhoneNumber($requestSms->sms->getSmsPhoneNumber())
        );
        $this->repository->add($sms);

        $this->response = new ResponseServiceSms($statusMessage);
    }

    public function getResponse(): ResponseServiceSms
    {
        return $this->response;
    }
}
