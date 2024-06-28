<?php

namespace Sms\Infrastructure\Api\V1;

use Exception;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Application\Services\Sms\SendSms;
use Sms\Domain\Model\SmsModel\FactorySmsBuilder;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Infrastructure\Persistence\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Safe\json_decode;

class SmsMakeController
{
    private const SENDING_CODE = 200;
    public function __construct(private SmsRepositoryMemory $repository, private OvhSmsSender $sendSms)
    {
    }
    #[Route('api/v1/sms/send', 'SendSms', methods: ['POST'])]
    public function execute(Request $request): Response
    {
        $request = $this->buildMakeSmsRequest($request);

        $sms = new SendSms($this->repository, $this->sendSms);

        try {
            $sms->execute($request);
        } catch (Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'statusCode' => '',
                    'data' => '',
                    'message' => "$e",
                ]
            );
        }
        $response = $sms->getResponse();
        return new JsonResponse(
            [
                'success' => true,
                'ErrorCode' => "",
                'data' => [
                    'statusMessage' => $response->statusMessage,
                ],
                'message' => "",
            ],
            self::SENDING_CODE
        );
    }

    private function buildMakeSmsRequest(Request $request): SendSmsRequest
    {
        $requestContent = $request->getContent();
        $data = json_decode($requestContent, true);
        /** @var array<string> $data*/
        $message = $data['messageText'];
        /** @var array<string> $phoneNumber
         * @var array<string> $data
        */
        $phoneNumber =  $data['phoneNumber'];
        $sms = FactorySmsBuilder::createsms($message, $phoneNumber, new SmsId());

        return new SendSmsRequest(new PhoneNumber($phoneNumber), new MessageText($message));
    }
}
