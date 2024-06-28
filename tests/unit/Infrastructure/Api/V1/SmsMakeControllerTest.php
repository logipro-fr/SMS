<?php

namespace Sms\Tests\Infrastructure\Api\V1;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response;
use Sms\Infrastructure\Api\V1\SmsMakeController;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

use function Safe\json_encode;

class SmsMakeControllerTest extends WebTestCase
{
    private const RESPONSE_OBJECT = "responseobject.json";
    private const SENDING_CODE = 200;

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient(["debug" => false]);
    }
    public function testControllerRouting(): void
    {
        $this->client->request(
            "POST",
            "api/v1/sms/send",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                "messageText" => "test",
                "phoneNumber" => "+33623456789",
            ]),
        );
        /** @var string */
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
    }


    public function testSmsControllerExecute(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => [["+33623456789"]]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $repository = new SmsRepositoryMemory();
        $sendSms = new OvhSmsSender($client);
        $controller = new SmsMakeController($repository, $sendSms);
        $request = Request::create(
            "/api/V1/Sms/",
            "GET",
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                "messageText" => "test",
                "phoneNumber" => "+33623456789",
            ]),
        );
        $response = $controller->execute($request);
        /** @var string */
        $responseContent = $response->getContent();

        $this->assertStringContainsString('"success":true', $responseContent);
        $this->assertStringContainsString('"ErrorCode":', $responseContent);
        $this->assertStringContainsString('"data":{"statusMessage":"Message sent successfully"}', $responseContent);
        $this->assertStringContainsString('"message":"', $responseContent);
    }



    public function testSmsControllerJsonrESPONSE(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => [["+33623456789"]]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $repository = new SmsRepositoryMemory();
        $sendSms = new OvhSmsSender($client);
        $controller = new SmsMakeController($repository, $sendSms);
        $request = Request::create(
            "/api/V1/Sms/",
            "GET",
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                "messageText" => "test",
                "phoneNumber" => "+33623456789",
            ]),
        );

        $response = $controller->execute($request);

        $content = $response->getContent();

        /** @var array<mixed> $responseData
         * @var string  $content
        */
        $responseData = json_decode($content, true);

        $this->assertTrue($responseData['success']);
        $this->assertEquals("", $responseData['ErrorCode']);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('statusMessage', $responseData['data']);
        $this->assertEquals('Message sent successfully', $responseData['data']['statusMessage']);
        $this->assertEquals('', $responseData['message']);
    }

    public function testSmsControllerCatchException(): void
    {
        $mock = new MockHandler([
        new RequestException("Error Communicating with Server", new Psr7Request('POST', 'test')),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $repository = new SmsRepositoryMemory();
        $sendSms = new OvhSmsSender($client);
        $controller = new SmsMakeController($repository, $sendSms);
        $request = Request::create(
            "/api/V1/Sms/",
            "GET",
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
            "messageText" => "test",
            "phoneNumber" => "+33623456789",
            ]),
        );

        $response = $controller->execute($request);
        $content = $response->getContent();

        /** @var array<mixed> $responseData
         * @var string  $content
        */
        $responseData = json_decode($content, true);

        $this->assertFalse($responseData['success']);
        $this->assertEquals('', $responseData['statusCode']);
        $this->assertEquals('', $responseData['data']);
        $this->assertStringContainsString('Error Communicating with Server', $responseData['message']);
    }
}
