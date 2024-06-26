<?php

namespace Sms\Tests\Integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exception\SmsApiBadResponseException;
use Sms\Application\Services\Sms\ResponseServiceSms;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;
use Sms\Infrastructure\SmsProvider\Ovh\SendSms;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Symfony\Component\Dotenv\Dotenv;

class SmsIntegrationTest extends TestCase
{
    private const SENDING_MESSAGE = "Message sent successfully";
    private const MESSAGE = 'André Goutaire from Campus26 has just sent you a document to sign';
    private const PHONE_NUMBER = '+33123456789';

    protected function setUp(): void
    {
        parent::setUp();
        $dotenv = new Dotenv();
        $dotenv->loadEnv(getcwd() . '/src/Infrastructure/Shared/Symfony/.env.local');
    }


    public function testIntegrationSenderSms(): void
    {
        $httpClient = new Client();
        $sender = new SendSms($httpClient);

        $request = new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber([self::PHONE_NUMBER])
        ));
        $response = $sender->sendSms($request);

        $this->assertInstanceOf(ResponseServiceSms::class, $response);
        $this->assertEquals(self::SENDING_MESSAGE, $response);
    }


    public function testIntegrationSenderSmsFailure(): void
    {
        $httpClient = new Client();
        $sender = new SendSms($httpClient);

        $request = new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber([self::PHONE_NUMBER])
        ));

        $this->expectException(SmsApiBadResponseException::class);
        $this->expectExceptionMessage("An error happened while sending the message");

        $sender->sendSms($request);
    }
}
