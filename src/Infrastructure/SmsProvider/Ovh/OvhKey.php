<?php

namespace Sms\Infrastructure\SmsProvider\Ovh;

class OvhKey
{
    private string $applicationKey;
    private string $applicationSecret;
    private string $consumerKey;

    public function __construct()
    {
        $this->applicationKey = $_ENV['APPLICATION_KEY_OVH'];
        $this->applicationSecret = $_ENV['APPLICATION_SECRET_OVH'];
        $this->consumerKey = $_ENV['CONSUMER_KEY_OVH'];
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
