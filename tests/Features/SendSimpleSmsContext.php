<?php

namespace Features;

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Assert;
use Sms\Application\Services\Sms\RequestServiceSms;
use Sms\Application\Services\Sms\ResponseServiceSms;
use Sms\Application\Services\Sms\SmsService;
use Sms\Domain\Model\SmsModel\FactorySmsBuilder;
use Sms\Domain\Model\SmsModel\SmsId;
use Sms\Infrastructure\Persistence\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\SendSms;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Defines application features from the specific context.
 */
class SendSimpleSmsContext implements Context
{
    private ResponseServiceSms $response;
    private string $message;
    /** @var array<string> $phoneNumber */
    private array $phoneNumber;
    private RequestServiceSms $request;
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
     * @param string $messageText
     */
    public function theTextToSend($messageText): void
    {
        $this->message = $messageText;
    }

    /**
     * @Given the phone number is :phoneNumber
     * @param array<string> $phoneNumber
     */
    public function thePhoneNumberIs($phoneNumber): void
    {
        $this->phoneNumber = ['$phoneNumber'];
    }

    /**
     * @When the user submits a request to send SMS
     */
    public function submitsARequestToSendSms(): void
    {
        $this->request = FactorySmsBuilder::createRequestServiceSms($this->message, $this->phoneNumber, new SmsId());

        $mock = new MockHandler([
            new Response(self::SENDING_CODE, []),
            new Response(self::SENDING_CODE, [], Utils::streamFor(json_encode([self::RESPONSE_OBJECT]))),
            new Response(self::SENDING_CODE, [], Utils::streamFor(json_encode(["validReceivers" => ["+33123456789"]]))),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $smsApi = new SendSms($client);
        $repository = new SmsRepositoryMemory();
        $service = new SmsService($repository, $smsApi);


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
