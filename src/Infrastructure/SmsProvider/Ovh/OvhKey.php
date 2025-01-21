<?php

namespace Sms\Infrastructure\SmsProvider\Ovh;

class OvhKey
{
    private string $applicationKey;
    private string $applicationSecret;
    private string $consumerKey;

    public function __construct()
    {
        /** @var string $akey */
        $akey = $_ENV['APPLICATION_KEY_OVH'];
        /** @var string $asec */
        $asec = $_ENV['APPLICATION_SECRET_OVH'];
        /** @var string $ckey */
        $ckey = $_ENV['CONSUMER_KEY_OVH'];
        $this->applicationKey = $akey;
        $this->applicationSecret = $asec;
        $this->consumerKey = $ckey;
    }

    public function getApplicationKey(): string
    {
        return $this->applicationKey;
    }

    public function getApplicationSecret(): string
    {
        return $this->applicationSecret;
    }

    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }
}
