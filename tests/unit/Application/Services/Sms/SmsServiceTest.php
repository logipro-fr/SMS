<?php

namespace Sms\Tests\Application\Services\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;

class SmsServiceTest extends TestCase
{
    private const PHONE_NUMBER = "+33623456789";
    private const SENDING_CODE = 200;
    private const SENDING_MESSAGE = "Message sent successfully";
    private const RESPONSE_OBJECT = "./../ressourcesresponseobject.json";

    public function testStructureSendSmsService(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, ['/sms/']),
            new Response(
                self::SENDING_CODE,
                [],
                Utils::streamFor(json_encode([self::RESPONSE_OBJECT]))
            ),
            new Response(
                self::SENDING_CODE,
                [],
                Utils::streamFor(json_encode(["validReceivers" => [self::PHONE_NUMBER]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $service = new SendSmsService($repository, $smsApi);

        $requestSms = FactorySmsBuilder::createRequestServiceSms('test', self::PHONE_NUMBER);
        $service->execute($requestSms);
        $response1 = $service->getResponse();

        $this->assertEquals(self::SENDING_MESSAGE, $response1->statusMessage);
    }

    public function testExecute(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(
                self::SENDING_CODE,
                [],
                Utils::streamFor(json_encode([self::RESPONSE_OBJECT]))
            ),
            new Response(
                self::SENDING_CODE,
                [],
                Utils::streamFor(json_encode(["validReceivers" => [self::PHONE_NUMBER]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $SendSmsServiceProvider = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $sut = new SendSmsService($repository, $SendSmsServiceProvider);

        $requestSms = FactorySmsBuilder::createRequestServiceSms('Test', self::PHONE_NUMBER);

        $sut->execute($requestSms);
        $response = $sut->getResponse();
        $savedSms = $repository->findById($response->smsId);

        $this->assertEquals(self::SENDING_MESSAGE, $response->statusMessage);
        $this->assertEquals(new MessageText('Test'), $savedSms->getSmsMessage());
        $this->assertEquals(self::PHONE_NUMBER, $savedSms->getSmsPhoneNumber());
    }
}
