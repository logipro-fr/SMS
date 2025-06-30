<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;

class SmsTest extends TestCase
{
    private const MESSAGE = 'André Goutaire from Campus26 has just sent you a document to sign';
    private const PHONE_NUMBER = '+33623456789';
    private const TEST_ID = 'TestId';

    public function testSms(): void
    {
        $message = new MessageText(self::MESSAGE);
        $phoneNumber = new MobilePhoneNumber(self::PHONE_NUMBER);

        $factory = new FactorySmsBuilder();
        $sms = $factory->createSms($message, $phoneNumber);

        $this->assertEquals(self::MESSAGE, $sms->getSmsMessage());
        $this->assertEquals(self::PHONE_NUMBER, $sms->getSmsPhoneNumber());
        $this->assertStringStartsWith("sms_", $sms->getId());
    }

    public function testSmsRepository(): void
    {
        $message = new MessageText(self::MESSAGE);
        $phoneNumber = new MobilePhoneNumber(self::PHONE_NUMBER);
        $smsId = new SmsId(self::TEST_ID);

        $factory = new FactorySmsBuilder();
        $smsWithId = $factory->createSms($message, $phoneNumber, $smsId);

        $this->assertEquals(self::TEST_ID, $smsWithId->getId());
    }
}
