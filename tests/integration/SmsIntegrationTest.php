<?php

namespace Sms\Tests\Integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\Exceptions\SmsApiBadReceiversException;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
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

        /** @var string $phone */
        $phone = $_ENV['PHONE_NUMBER'];
        $response = $smsApi->sendSmsService(
            new MobilePhoneNumber($phone),
            new MessageText(self::MESSAGE)
        );

        $this->assertEquals(self::SENDING_MESSAGE, $response->getStatusMessage());
    }

    public function testShouldThrowOnInvalidNumber(): void
    {
        $this->expectException(SmsApiBadReceiversException::class);

        $client = new Client();
        $smsApi = new OvhSmsSender($client);

        $smsApi->sendSmsService(
            new MobilePhoneNumber('+33000000000'),
            new MessageText('Test')
        );
    }
}
