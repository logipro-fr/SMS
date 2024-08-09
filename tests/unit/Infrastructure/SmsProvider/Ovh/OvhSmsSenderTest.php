<?php

namespace Sms\Tests\Infrastructure\SmsProvider\Ovh;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exceptions\SmsApiBadReceiversException;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Symfony\Component\Dotenv\Dotenv;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;

class OvhSmsSenderTest extends TestCase
{
    private const MESSAGE = "André Goutaire from Campus26 has just sent you a document to sign.";
    private const PHONE_NUMBER = '+33623456789';
    private const RESPONSE_OBJECT = "responseobject.json";
    private const SENDING_CODE = 200;
    private const SENDING_MESSAGE = "Message sent successfully";
    private const LINK = '/../../../../ressources/responseobject.json';
    private const ERROR_RECEIVERS = "Error sending message, check recipient!";

    private string $messageText;
    private string $phoneNumber;


    protected function setUp(): void
    {
        parent::setUp();

        $this->phoneNumber = self::PHONE_NUMBER;
        $this->messageText = self::MESSAGE;
        $dotenv = new Dotenv();
        $dotenv->loadEnv(getcwd() . '/src/Infrastructure/Shared/Symfony/.env');
    }

    public function testSendSms(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => ["+33623456789"]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);

        $response = $smsApi->sendSms(
            new MobilePhoneNumber(self::PHONE_NUMBER),
            new MessageText(self::MESSAGE)
        );

        $this->assertEquals(self::SENDING_MESSAGE, $response->getStatusMessage());
    }

    public function testRequestPhoneNumber(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => ["+33623456789"]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);
        $smsApi->sendSms(
            new MobilePhoneNumber(self::PHONE_NUMBER),
            new MessageText(self::MESSAGE)
        );

        $jsonString = file_get_contents(__DIR__ . self::LINK);
        /** @var \stdClass */
        $jsonDecode = json_decode($jsonString);

        $validReceivers = $jsonDecode->validReceivers;

        $this->assertSame([[self::PHONE_NUMBER]], $validReceivers);
    }

    public function testGetApiKey(): void
    {
        $applicationkeyOvh = getenv('APPLICATION_KEY_OVH');
        $applicationsecretkeyOvh = getenv('APPLICATION_SECRET_OVH');
        $consumerkeyOvh = getenv('CONSUMER_KEY_OVH');

        $this->assertNotEmpty($applicationkeyOvh);
        $this->assertNotEmpty($applicationsecretkeyOvh);
        $this->assertNotEmpty($consumerkeyOvh);
    }

    public function testGetContent()
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["receivers" => ["+33623456789"]])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sender = new OvhSmsSender($client);
        $phoneNumber = '0612345678';
        $messageText = 'Hello World';

        $expectedContent = [
            'charset' => 'UTF-8',
            'class' => 'phoneDisplay',
            'coding' => '7bit',
            'message' => $messageText,
            'noStopClause' => false,
            'priority' => 'high',
            'receivers' => [$phoneNumber],
            'senderForResponse' => true,
            'validityPeriod' => 2880,
        ];

        $this->assertEquals($expectedContent, $sender->getContent($messageText, $phoneNumber));
    }

    public function testSendSmsThrowsExceptionForInvalidReceivers(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => ""])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);

        $this->expectException(SmsApiBadReceiversException::class);
        $this->expectExceptionMessage(self::ERROR_RECEIVERS);


        $smsApi->sendSms(
            new MobilePhoneNumber(self::PHONE_NUMBER),
            new MessageText(self::MESSAGE)
        );
    }
}
