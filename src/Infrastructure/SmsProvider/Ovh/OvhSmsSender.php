<?php

namespace Sms\Infrastructure\SmsProvider\Ovh;

use GuzzleHttp\Client;
use Ovh\Api;
use Sms\Application\Services\Sms\Exceptions\SmsApiBadReceiversException;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\StatusMessage;
use Sms\Application\Services\Sms\SenderProviderInterface;
use Sms\Domain\Model\Sms\MobilePhoneNumber;

class OvhSmsSender implements SenderProviderInterface
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


    public function __construct(
        private ?Client $http_client
    ) {
        $this->http_client = $http_client;
    }


    public function sendSms(MobilePhoneNumber $phoneNumber, MessageText $message): StatusMessage
    {
        $content = $this->getContent(
            $message,
            $phoneNumber->getNumber(),
        );

        $key = new OvhKey();

        $conn = new Api(
            $key->getApplicationKey(),
            $key->getApplicationSecret(),
            'ovh-eu',
            $key->getConsumerKey(),
            $this->http_client,
        );

        $smsServices = $conn->get('/sms/');
        $response = $conn->post(sprintf(self::LINK_OVH, $smsServices[self::SERVICE_NUMBER_OVH]), $content);

        if ($response['validReceivers']) {
            return new StatusMessage(self::SENDING_MESSAGE);
        }
        throw new SmsApiBadReceiversException(self::ERROR_RECEIVERS);
    }

    /**
     * @return array<string, array<string>|bool|int|string>
     */
    public function getContent(string $messagetext, string $phoneNumber): array
    {
        return [
            "charset" => self::CHARSET,
            "class" => self::MESSAGECLASS,
            "coding" => self::CODING,
            "message" => $messagetext,
            "noStopClause" => self::NOSTOPCLAUSE,
            "priority" => self::PRIORITY,
            "receivers" => [$phoneNumber],
            "senderForResponse" => self::SENDERFORRESPONSE,
            "validityPeriod" => self::VALIDITYPERIOD
        ];
    }
}
