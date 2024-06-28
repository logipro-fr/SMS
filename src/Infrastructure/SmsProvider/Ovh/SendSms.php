<?php

namespace Sms\Infrastructure\SmsProvider\Ovh;

use GuzzleHttp\Client;
use Ovh\Api;
use Sms\Application\Services\Sms\Exception\SmsApiBadReceiversException;
use Sms\Domain\Model\SmsModel\StatusMessage;

class SendSms
{
    private const VALIDITYPERIOD = 2880;
    private const NOSTOPCLAUSE = false;
    private const CHARSET = "UTF-8";
    private const MESSAGECLASS = "phoneDisplay";
    private const CODING  = "7bit";
    private const PRIORITY = "high";
    private const SENDERFORRESPONSE = true;

    private const SENDING_MESSAGE = "Message sent successfully";
    private const ERROR_RECEIVERS = "Error sending message, check recipient!";

    private const LINK_OVH = "/sms/%s/jobs";
    private const SERVICE_NUMBER_OVH = 0;

    private string $APPLICATION_KEY_OVH;
    private string $APPLICATION_SECRET_OVH;
    private string $CONSUMER_KEY_OVH;

    public function __construct(
        private ?Client $http_client,
        ?string $applicationKey = null,
        ?string $applicationSecretKey = null,
        ?string $consumerKey = null,
    ) {
        $this->http_client = $http_client;

        if ($applicationKey == null) {
            $applicationKey = $_ENV["APPLICATION_KEY_OVH"];
        }
        $this->APPLICATION_KEY_OVH = $applicationKey;

        if ($applicationSecretKey == null) {
            $applicationSecretKey = $_ENV["APPLICATION_SECRET_OVH"];
        }
        $this->APPLICATION_SECRET_OVH = $applicationSecretKey;

        if ($consumerKey == null) {
            $consumerKey = $_ENV["CONSUMER_KEY_OVH"];
        }
        $this->CONSUMER_KEY_OVH = $consumerKey;
    }


    public function sendSms(RequestSms $requestSms): StatusMessage
    {
        $content = $this->getContent(
            $requestSms->sms->getSmsMessage(),
            $requestSms->sms->getSmsPhoneNumber(),
        );

        $conn = new Api(
            $this->APPLICATION_KEY_OVH,
            $this->APPLICATION_SECRET_OVH,
            'ovh-eu',
            $this->CONSUMER_KEY_OVH,
            $this -> http_client,
        );

        $smsServices = $conn->get('/sms/');
        $response = $conn->post(sprintf(self::LINK_OVH, $smsServices[self::SERVICE_NUMBER_OVH]), $content);

        if ($response['validReceivers']) {
            return new StatusMessage(self::SENDING_MESSAGE);
        } else {
            throw new SmsApiBadReceiversException(self::ERROR_RECEIVERS);
        }
    }


    /**
    * @param string $messagetext
    * @param array<string> $phoneNumber
    * @return array<string, array<string>|bool|int|string>
    */
    public function getContent(string $messagetext, array $phoneNumber): array
    {
        return [
            "charset" => self::CHARSET,
            "class" => self::MESSAGECLASS,
            "coding" => self::CODING,
            "message" => $messagetext,
            "noStopClause" => self::NOSTOPCLAUSE,
            "priority" => self::PRIORITY,
            "receivers" => $phoneNumber,
            "senderForResponse" => self::SENDERFORRESPONSE,
            "validityPeriod" => self::VALIDITYPERIOD
        ];
    }
}
