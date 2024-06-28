<?php

namespace Sms\Tests\Integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exception\SmsApiBadReceiversException;
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
        $client = new Client();

        $smsApi = new SendSms($client);

        $response = $smsApi->sendSms(new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber([self::PHONE_NUMBER])
        )));

        $this->assertEquals(self::SENDING_MESSAGE, $response->getStatusMessage());
    }


    public function testIntegrationSenderSmsBadReceiver(): void
    {
        $httpClient = new Client();
        $sender = new SendSms($httpClient);

        $request = new RequestSms(new Sms(
            new MessageText(self::MESSAGE),
            new PhoneNumber([])
        ));

        $this->expectException(SmsApiBadReceiversException::class);
        $this->expectExceptionMessage("Error sending message, check recipient!");

        $sender->sendSms($request);
    }
}
