<?php

namespace Sms\Tests\Infrastructure\SmsProvider\Ovh;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exception\SmsApiBadReceiversException;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Infrastructure\SmsProvider\Ovh\SendSms;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;
use Symfony\Component\Dotenv\Dotenv;

use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;

class SmsApiTest extends TestCase
{
    private const MESSAGE = "André Goutaire from Campus26 has just sent you a document to sign.";
    private const PHONE_NUMBER = ['+33123456789'];
    private const RESPONSE_OBJECT = "responseobject.json";
    private const SENDING_CODE = 200;
    private const SENDING_MESSAGE = "Message sent successfully";
    private const LINK = '/../../../../ressources/responseobject.json';
    private const ERROR_RECEIVERS = "Error sending message, check recipient!";

    private string $messageText;
    /**
    * @var string[]
    */
    private array $phoneNumber = [];


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
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => ["+33123456789"]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new SendSms($client);

        $response = $smsApi->sendSms(new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber(self::PHONE_NUMBER)
        )));

        $this->assertEquals(self::SENDING_MESSAGE, $response->getStatusMessage());
    }

    public function testSendSmsThrowsExceptionForInvalidReceivers(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => []])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new SendSms($client);

        $this->expectException(SmsApiBadReceiversException::class);
        $this->expectExceptionMessage(self::ERROR_RECEIVERS);


        $smsApi->sendSms(new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber(self::PHONE_NUMBER)
        )));
    }




    public function testOrderContent(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, [], json_encode(["charset"])),
            new Response(self::SENDING_CODE, [], json_encode(["class"])),
            new Response(self::SENDING_CODE, [], json_encode(["coding"])),
            new Response(self::SENDING_CODE, [], json_encode(["message"])),
            new Response(self::SENDING_CODE, [], json_encode(["noStopClause"])),
            new Response(self::SENDING_CODE, [], json_encode(["priority"])),
            new Response(self::SENDING_CODE, [], json_encode(["receivers"])),
            new Response(self::SENDING_CODE, [], json_encode(["senderForResponse"])),
            new Response(self::SENDING_CODE, [], json_encode(["validityPeriod"])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $smsApi = new SendSms($client,);

        $content = $smsApi->getContent($this->messageText, $this->phoneNumber);
        $expectedOrder = [
            "charset",
            "class",
            "coding",
            "message",
            "noStopClause",
            "priority",
            "receivers",
            "senderForResponse",
            "validityPeriod"
        ];

        $content = $smsApi->getContent($this->messageText, $this->phoneNumber);
        $actualOrder = array_keys($content);
        $this->assertSame($expectedOrder, $actualOrder);
    }





    public function testRequestPhoneNumber(): void
    {
        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], json_encode([self::RESPONSE_OBJECT])),
            new Response(self::SENDING_CODE, [], json_encode(["validReceivers" => [["+33123456789"]]])),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new SendSms($client);
        $smsApi->sendSms(new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber(self::PHONE_NUMBER)
        )));

        $jsonString = file_get_contents(__DIR__ . self::LINK);
        /** @var \stdClass */
        $jsonDecode = json_decode($jsonString);

        $validReceivers = $jsonDecode->validReceivers[0];

        $this->assertSame(self::PHONE_NUMBER, $validReceivers);
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
}
