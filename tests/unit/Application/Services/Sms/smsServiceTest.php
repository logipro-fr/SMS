<?php

namespace tests\unit\Application\Services\Sms;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\RequestServiceSms;
use Sms\Application\Services\Sms\SmsService;
use Sms\Domain\Model\SmsModel\FactorySmsBuilder;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsFactory;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Infrastructure\Persistence\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\SendSms;

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
                Utils::streamFor(json_encode(["validReceivers" => ["+33123456789"]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new SendSms($client);
        $repository = new SmsRepositoryMemory();
        $service = new SmsService($repository, $smsApi);

        $requestSms = FactorySmsBuilder::createRequestServiceSms('test', ['+33123456789'], new SmsId('test'));
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
                Utils::streamFor(json_encode(["validReceivers" => ["+33123456789"]]))
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sendSmsProvider = new SendSms($client);
        $repository = new SmsRepositoryMemory();
        $service = new SmsService($repository, $sendSmsProvider);

        $smsId = new SmsId('test');

        $requestSms = FactorySmsBuilder::createRequestServiceSms('Test', ['+33123456789'], $smsId);


        $service->execute($requestSms);
        $response = $service->getResponse();

        $this->assertEquals(self::SENDING_MESSAGE, $response->statusMessage);

        $repository->add($requestSms->sms);

        $foundSms1 = $repository->findById($requestSms->sms->getId());

        $this->assertEquals('test', $foundSms1->getId());

        $savedSms = $repository->findById($requestSms->sms->getId());
        $this->assertNotNull($savedSms);

        $this->assertEquals($requestSms->sms->getSmsMessage(), $savedSms->getSmsMessage());
        $this->assertEquals($requestSms->sms->getSmsPhoneNumber(), $savedSms->getSmsPhoneNumber());
    }
}
