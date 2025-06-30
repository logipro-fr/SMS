<?php

namespace Sms\Infrastructure\Api\V1;

use Sms\Application\Services\Sms\Exceptions\SmsApiInvalidApiKeyException;
use Sms\Application\Services\Sms\Exceptions\SmsApiNoCreditException as ExceptionsSmsApiNoCreditException;
use Sms\Application\Services\Sms\Exceptions\SmsSendingException as ExceptionsSmsSendingException;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumberException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsMakeController
{
    public function __construct(private SendSmsService $sendSmsService)
    {
    }

    #[Route('/api/v1/sms/send', methods: ['POST'])]
    public function sendSms(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data) || !isset($data['phoneNumber'], $data['messageText'])) {
            return new JsonResponse([
                'success' => false,
                'data' => new \stdClass(),
                'error' => 'Paramètres invalides ou manquants',
                'error-message' => 'Paramètres manquants ou invalides'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            if (!is_string($data['phoneNumber']) || !is_string($data['messageText'])) {
                return new JsonResponse([
                    'success' => false,
                    'data' => new \stdClass(),
                    'error' => 'Type invalide',
                    'error-message' => 'Le numéro ou le message doit être une chaîne',
                ], Response::HTTP_BAD_REQUEST);
            }

            $phoneNumber = new MobilePhoneNumber($data['phoneNumber']);
            $messageText = new MessageText($data['messageText']);

            $sendSmsRequest = new SendSmsRequest($phoneNumber, $messageText);

            $sendSmsResponse = $this->sendSmsService->execute($sendSmsRequest);

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'statusMessage' => $sendSmsResponse->statusMessage,
                    'smsId' => (string) $sendSmsResponse->smsId,
                ],
                'error' => '',
                'error-message' => ''
            ], Response::HTTP_OK);
        } catch (ExceptionsSmsApiNoCreditException $e) {
            return new JsonResponse([
                'success' => false,
                'data' => new \stdClass(),
                'error' => 'SmsApiNoCreditException',
                'error-message' => $e->getMessage(),
            ], Response::HTTP_PAYMENT_REQUIRED); // 402
        } catch (SmsApiInvalidApiKeyException $e) {
            return new JsonResponse([
                'success' => false,
                'data' => new \stdClass(),
                'error' => 'InvalidApiKeyException',
                'error-message' => $e->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        } catch (ExceptionsSmsSendingException $e) {
            return new JsonResponse([
                'success' => false,
                'data' => new \stdClass(),
                'error' => 'SmsSendingException',
                'error-message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'data' => new \stdClass(),
                'error' => 'Erreur interne',
                'error-message' => 'Une erreur interne est survenue',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
