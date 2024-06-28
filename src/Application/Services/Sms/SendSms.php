<?php

namespace Sms\Application\Services\Sms;

use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Application\Services\Sms\SendSmsResponse;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsRepositoryInterface;
use Sms\Domain\Model\SmsModel\StatusMessage;

class SendSms
{
    public SendSmsResponse $response;

    public function __construct(private SmsRepositoryInterface $repository, private OvhSmsSender $sendSms)
    {
        $this->repository = $repository;
        $this->sendSms = $sendSms;
    }

    public function execute(SendSmsRequest $requestSms): void
    {
        $statusMessageObject = $this->sendSms->sendSms($requestSms->phoneNumber, $requestSms->message);
        $statusMessageText = $statusMessageObject->getStatusMessage();

        $statusMessage = new StatusMessage($statusMessageText);

        $sms = new Sms($requestSms->message, $requestSms->phoneNumber);
        $this->repository->add($sms);

        $this->response = new SendSmsResponse($statusMessage, $sms->getId());
    }

    public function getResponse(): SendSmsResponse
    {
        return $this->response;
    }
}
