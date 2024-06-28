<?php

namespace Features;

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Assert;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Application\Services\Sms\SendSmsResponse;
use Sms\Application\Services\Sms\SendSms;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Defines application features from the specific context.
 */
class SendSimpleSmsContext implements Context
{
    private SendSmsResponse $response;
    private string $message;
    private string $phoneNumber;
    private SendSmsRequest $request;
    private const RESPONSE_OBJECT = "responseobject.json";
    private const SENDING_CODE = 200;
    private const SENDING_MESSAGE = "Message sent successfully";


    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv(getcwd() . '/src/Infrastructure/Shared/Symfony/.env');
    }

     /**
     * @Given the text to send :messageText
     */
    public function theTextToSend(string $messageText): void
    {
        $this->message = $messageText;
    }

    /**
     * @Given the phone number is :phoneNumber
     */
    public function thePhoneNumberIs(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @When the user submits a request to send SMS
     */
    public function submitsARequestToSendSms(): void
    {
        $this->request = FactorySmsBuilder::createRequestServiceSms($this->message, $this->phoneNumber);

        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], Utils::streamFor(json_encode([self::RESPONSE_OBJECT]))),
            new Response(self::SENDING_CODE, [], Utils::streamFor(json_encode(["validReceivers" => ["+33623456789"]]))),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $service = new SendSms($repository, $smsApi);


        $service->execute($this->request);

        $this->response = $service->getResponse();
    }

    /**
     * @Then the system sends the SMS to the specified number
     */
    public function theSystemSendsTheSmsToTheSpecifiedNumber(): void
    {
    }

    /**
     * @Then the system returns an acknowledgment indicating that the message has been transmitted to the SMS service \
     * provider
     */
    public function returnsAnAcknowledgment(): void
    {
        Assert::assertEquals(self::SENDING_MESSAGE, $this->response->statusMessage);
    }
}
