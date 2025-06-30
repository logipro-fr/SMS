<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\Sms;
use Sms\Application\Services\Sms\SendSmsRequest;

class FactorySmsBuilderTest extends TestCase
{
    private FactorySmsBuilder $factory;

    protected function setUp(): void
    {
        $this->factory = new FactorySmsBuilder();
    }

    public function testCreateSmsWithGeneratedId(): void
    {
        $message = new MessageText('Hello World');
        $phone = new MobilePhoneNumber('+33612345678');

        $sms = $this->factory->createSms($message, $phone);

        $this->assertInstanceOf(Sms::class, $sms);
        $this->assertEquals($message, $sms->getSmsMessage());
        $this->assertEquals($phone->getNumber(), $sms->getSmsPhoneNumber());
        $this->assertInstanceOf(SmsId::class, $sms->getId());
    }

    public function testCreateSmsWithProvidedId(): void
    {
        $message = new MessageText('Hello again');
        $phone = new MobilePhoneNumber('+33612345678');
        $smsId = new SmsId('custom_id');

        $sms = $this->factory->createSms($message, $phone, $smsId);

        $this->assertEquals($smsId, $sms->getId());
    }

    public function testCreateRequestServiceSms(): void
    {
        $message = new MessageText('Request message');
        $phone = new MobilePhoneNumber('+33612345678');

        $request = $this->factory->createRequestServiceSms($message, $phone);

        $this->assertInstanceOf(SendSmsRequest::class, $request);
        $this->assertEquals($phone, $request->phoneNumber);
        $this->assertEquals($message, $request->messageText);
    }
}
