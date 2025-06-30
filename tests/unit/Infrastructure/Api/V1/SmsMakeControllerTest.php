<?php

namespace Sms\Tests\Infrastructure\Api\V1;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sms\Infrastructure\Api\V1\SmsMakeController;
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Application\Services\Sms\SendSmsResponse;
use Sms\Domain\Model\Sms\SmsId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SmsMakeControllerTest extends TestCase
{
    private SmsMakeController $controller;

    /** @var MockObject&SendSmsService */
    private SendSmsService $sendSmsServiceMock;

    protected function setUp(): void
    {
        $this->sendSmsServiceMock = $this->createMock(SendSmsService::class);
        $this->controller = new SmsMakeController($this->sendSmsServiceMock);
    }

    public function testSendSmsReturnsBadRequestIfParametersMissing(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode([]) ?: '');

        $response = $this->controller->sendSms($request);

        /** @var array{error: string} $data */
        $data = json_decode($response->getContent() ?: '', true);

        $this->assertEquals('Paramètres invalides ou manquants', $data['error']);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testSendSmsCallsServiceAndReturnsSuccess(): void
    {
        $phoneNumber = '+33601020304';
        $messageText = 'Test message';
        $smsId = 'abc123';

        $request = new Request([], [], [], [], [], [], json_encode([
            'phoneNumber' => $phoneNumber,
            'messageText' => $messageText,
        ]) ?: '');

        $expectedResponse = new SendSmsResponse('Sent successfully', new SmsId($smsId));

        $this->sendSmsServiceMock->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(SendSmsRequest::class))
            ->willReturn($expectedResponse);

        $response = $this->controller->sendSms($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        /** @var array{status: string, smsId: string} $responseData */
        $responseData = json_decode($response->getContent() ?: '', true);
        $this->assertEquals('Sent successfully', $responseData['data']['statusMessage']);
        $this->assertEquals($smsId, $responseData['data']['smsId']);
    }

    public function testSendSmsReturnsErrorOnException(): void
    {
        $phoneNumber = '+33601020304';
        $messageText = 'Test message';

        $request = new Request([], [], [], [], [], [], json_encode([
            'phoneNumber' => $phoneNumber,
            'messageText' => $messageText,
        ]) ?: '');

        $this->sendSmsServiceMock->expects($this->once())
            ->method('execute')
            ->willThrowException(new \Exception('Erreur interne'));

        $response = $this->controller->sendSms($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        /** @var array{error: string} $data */
        $data = json_decode($response->getContent() ?: '', true);
        $this->assertEquals('Erreur interne', $data['error']);
    }
}
