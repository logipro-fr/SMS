<?php

namespace Sms\Tests\Application\Services\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\SendSms;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;

class SmsServiceTest extends TestCase
{
    private const SENDING_CODE = 200;
    private const SENDING_MESSAGE = "Message sent successfully";
    private const RESPONSE_OBJECT = "./../ressourcesresponseobject.json";

    public function testStructureSendSms(): void
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
                Utils::streamFor(json_encode(["validReceivers" => ["+33623456789"]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $service = new SendSms($repository, $smsApi);

        $requestSms = FactorySmsBuilder::createRequestServiceSms('test', '+33623456789');
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
                Utils::streamFor(json_encode(["validReceivers" => ["+33623456789"]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sendSmsProvider = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $sut = new SendSms($repository, $sendSmsProvider);

        $requestSms = FactorySmsBuilder::createRequestServiceSms('Test', '+33623456789');

        $sut->execute($requestSms);
        $response = $sut->getResponse();
        $savedSms = $repository->findById($response->smsId);

        $this->assertEquals(self::SENDING_MESSAGE, $response->statusMessage);
        $this->assertNotNull($savedSms);
        $this->assertEquals(new MessageText('Test'), $savedSms->getSmsMessage());
        $this->assertEquals('+33623456789', $savedSms->getSmsPhoneNumber());
    }
}
