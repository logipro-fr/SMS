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
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Infrastructure\Persistence\Sms\SmsRepositoryMemory;
use Sms\Infrastructure\SmsProvider\Ovh\OvhSmsSender;
use Symfony\Component\Dotenv\Dotenv;

class SendNoCreditSmsContext implements Context
{
    private SendSmsResponse $response;
    private string $message;
    private string $phoneNumber;
    private SendSmsRequest $request;
    private const ERROR_MESSAGE = "Insufficient SMS credit";
    private const RESPONSE_OBJECT = "responseobject.json";
    private FactorySmsBuilder $factory;


    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->loadEnv(getcwd() . '/src/Infrastructure/Shared/Symfony/.env');
        $dotenv->loadEnv(getcwd() . '/secret.env');

        $this->factory = new FactorySmsBuilder();
    }

    /**
     * @Given the message content is :messageText
     */
    public function theMessageContentIs(string $messageText): void
    {
        $this->message = $messageText;
    }

    /**
     * @Given the recipient's phone number is :phoneNumber
     */
    public function theRecipientsPhoneNumberIs(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @When the SMS sending request is triggered
     */
    public function theSmsSendingRequestIsTriggered(): void
    {
        $this->request = $this->factory->createRequestServiceSms(
            new MessageText($this->message),
            new MobilePhoneNumber($this->phoneNumber)
        );

        $mock = new MockHandler([
            new Response(200, [], Utils::streamFor(json_encode([]))),
            new Response(200, [], Utils::streamFor(json_encode([self::RESPONSE_OBJECT]))),
            new Response(403, [], Utils::streamFor(json_encode(["message" => "not enough credit"]))),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client = new Client([
            'handler' => $handlerStack,
            'http_errors' => false,
        ]);

        $smsApi = new OvhSmsSender($client);
        $repository = new SmsRepositoryMemory();
        $factory = new FactorySmsBuilder();
        $service = new SendSmsService($repository, $smsApi, $factory);


        try {
            $this->response = $service->execute($this->request);
        } catch (\Exception $e) {
            $fakeSmsId = new SmsId('error');
            $this->response = new SendSmsResponse($e->getMessage(), $fakeSmsId);
        }
    }


    /**
     * @Then the operation should fail to send the SMS
     */
    public function theOperationShouldFailToSendTheSms(): void
    {
        Assert::assertNotEquals('Message sent successfully', $this->response->statusMessage);
    }

    /**
     * @Then an error should indicate that there is not enough SMS credit
     */
    public function anErrorShouldIndicateThatThereIsNotEnoughSmsCredit(): void
    {
        Assert::assertEquals(self::ERROR_MESSAGE, $this->response->statusMessage);
    }
}
