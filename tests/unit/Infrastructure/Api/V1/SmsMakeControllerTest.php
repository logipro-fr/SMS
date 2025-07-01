<?php

namespace Tests\Sms\Infrastructure\Api\V1;

use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exceptions\SmsApiInvalidApiKeyException;
use Sms\Application\Services\Sms\Exceptions\SmsApiNoCreditException;
use Sms\Application\Services\Sms\Exceptions\SmsSendingException;
use Sms\Application\Services\Sms\SendSmsResponse;
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Api\V1\SmsMakeController;
use Symfony\Component\HttpFoundation\Request;

class SmsMakeControllerTest extends TestCase
{
    /** @var SendSmsService&\PHPUnit\Framework\MockObject\MockObject */
    private SendSmsService $sendSmsService;

    private SmsMakeController $controller;

    protected function setUp(): void
    {
        $this->sendSmsService = $this->createMock(SendSmsService::class);
        $this->controller = new SmsMakeController($this->sendSmsService);
    }

    public function testEmptyPayloadReturnsBadRequest(): void
    {
        $jsonPayload = json_encode([]);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(400, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('Paramètres invalides ou manquants', $data['error']);
    }

    public function testMissingParametersReturnsBadRequest(): void
    {
        $payload = ['phoneNumber' => '+33612345678'];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(400, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('Paramètres invalides ou manquants', $data['error']);
    }

    public function testWrongTypeParametersReturnsBadRequest(): void
    {
        $payload = [
            'phoneNumber' => 123456,
            'messageText' => ['not', 'a', 'string'],
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(400, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('Type invalide', $data['error']);
    }

    public function testSuccessfulSmsSending(): void
    {
        $this->sendSmsService
            ->method('execute')
            ->willReturn(new SendSmsResponse('Envoyé', new SmsId('abc123')));

        $payload = [
            'phoneNumber' => '+33612345678',
            'messageText' => 'Hello',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);

        $this->assertSame('Envoyé', $data['data']['statusMessage']);
        $this->assertSame('abc123', $data['data']['smsId']);
    }

    public function testNoCreditReturns402(): void
    {
        $this->sendSmsService
            ->method('execute')
            ->willThrowException(new SmsApiNoCreditException('Plus de crédit'));

        $payload = [
            'phoneNumber' => '+33612345678',
            'messageText' => 'Message',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(402, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('SmsApiNoCreditException', $data['error']);
        $this->assertSame('Plus de crédit', $data['error-message']);
    }

    public function testInvalidApiKeyReturns403(): void
    {
        $this->sendSmsService
            ->method('execute')
            ->willThrowException(new SmsApiInvalidApiKeyException('Clé invalide'));

        $payload = [
            'phoneNumber' => '+33612345678',
            'messageText' => 'Message',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(403, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('SmsApiInvalidApiKeyException', $data['error']);
        $this->assertSame('Clé invalide', $data['error-message']);
    }

    public function testSmsSendingExceptionReturns500(): void
    {
        $this->sendSmsService
            ->method('execute')
            ->willThrowException(new SmsSendingException('Erreur SMS'));

        $payload = [
            'phoneNumber' => '+33612345678',
            'messageText' => 'Message',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(500, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('SmsSendingException', $data['error']);
        $this->assertSame('Erreur SMS', $data['error-message']);
    }

    public function testGenericExceptionReturns500(): void
    {
        $this->sendSmsService
            ->method('execute')
            ->willThrowException(new \RuntimeException('Inconnue'));

        $payload = [
            'phoneNumber' => '+33612345678',
            'messageText' => 'Test',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(500, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('Erreur interne', $data['error']);
        $this->assertSame('Une erreur interne est survenue', $data['error-message']);
    }

    public function testPhoneNumberWrongTypeReturnsBadRequest(): void
    {
        $payload = [
            'phoneNumber' => ['not', 'a', 'string'],
            'messageText' => 'Hello',
        ];
        $jsonPayload = json_encode($payload);
        $this->assertIsString($jsonPayload);

        $request = Request::create('/api/v1/sms/send', 'POST', [], [], [], [], (string) $jsonPayload);
        $response = $this->controller->sendSms($request);

        $this->assertEquals(400, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertIsString($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);

        $this->assertFalse($data['success']);
        $this->assertSame('Type invalide', $data['error']);
        $this->assertSame('Le numéro ou le message doit être une chaîne', $data['error-message']);
    }
}
