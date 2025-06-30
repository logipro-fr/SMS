<?php

namespace Sms\Application\Services\Sms;

use Sms\Application\Services\Sms\Exceptions\SmsApiBadReceiversException;
use Sms\Application\Services\Sms\Exceptions\SmsApiInvalidApiKeyException;
use Sms\Application\Services\Sms\Exceptions\SmsApiNoCreditException;
use Sms\Application\Services\Sms\Exceptions\SmsSendingException;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumberException;
use Sms\Domain\Model\Sms\SmsRepositoryInterface;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\StatusMessage;

class SendSmsService
{
    public function __construct(
        private SmsRepositoryInterface $repository,
        private SenderProviderInterface $senderProvider,
        private FactorySmsBuilder $smsFactory
    ) {
    }

    public function execute(SendSmsRequest $requestSms): SendSmsResponse
    {
        try {
            $statusMessageObject = $this->senderProvider->sendSmsService(
                $requestSms->phoneNumber,
                $requestSms->messageText
            );
            $statusMessageText = $statusMessageObject->getStatusMessage();
            $statusMessage = new StatusMessage($statusMessageText);

            $sms = $this->smsFactory->createSms($requestSms->messageText, $requestSms->phoneNumber);

            $this->repository->add($sms);

            return new SendSmsResponse($statusMessage, $sms->getId());
        } catch (
            SmsApiNoCreditException |
                 SmsApiInvalidApiKeyException |
                 SmsApiBadReceiversException $e
        ) {
            throw $e;
        } catch (\Exception $e) {
            throw new SmsSendingException('Erreur lors de l’envoi du SMS', 0, $e);
        }
    }
}
