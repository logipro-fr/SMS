<?php

namespace Sms\Tests\Integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exception\SmsApiBadReceiversException;
use Sms\Infrastructure\SmsProvider\Ovh\RequestSms;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Symfony\Component\Dotenv\Dotenv;

class SmsIntegrationTest extends TestCase
{
    private const SENDING_MESSAGE = "Message sent successfully";
    private const MESSAGE = 'André Goutaire from Campus26 has just sent you a document to sign';

    protected function setUp(): void
    {
        parent::setUp();
        $dotenv = new Dotenv();
        $dotenv->overload(getcwd() . '/src/Infrastructure/Shared/Symfony/.env.local');
    }


    public function testIntegrationSenderSms(): void
    {
        $client = new Client();

        $smsApi = new OvhSmsSender($client);

        $response = $smsApi->sendSms(
            new PhoneNumber([$_ENV['PHONE_NUMBER']]),
            new MessageText(self::MESSAGE)
        );

        $this->assertEquals(self::SENDING_MESSAGE, $response->getStatusMessage());
    }


    public function testIntegrationSenderSmsBadReceiver(): void
    {
        $httpClient = new Client();
        $sender = new OvhSmsSender($httpClient);

        $this->expectException(SmsApiBadReceiversException::class);
        $this->expectExceptionMessage("Error sending message, check recipient!");

        $sender->sendSms(new PhoneNumber([]), new MessageText(self::MESSAGE));
    }
}
