<?php

namespace Sms\Tests\Infrastructure\SmsProvider\Ovh;

use PHPUnit\Framework\TestCase;
use Sms\Infrastructure\SmsProvider\Ovh\OvhKey;

class OvhKeyTest extends TestCase
{
    public function testDefaultValue(): void
    {
        $key = new OvhKey();

        $this->assertEquals($_ENV['APPLICATION_KEY_OVH'], $key->getApplicationKey());
        $this->assertEquals($_ENV['APPLICATION_SECRET_OVH'], $key->getApplicationSecret());
        $this->assertEquals($_ENV['CONSUMER_KEY_OVH'], $key->getConsumerKey());
    }
}
