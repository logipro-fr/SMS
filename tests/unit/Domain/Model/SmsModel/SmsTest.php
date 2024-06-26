<?php

namespace Sms\Domain\Model\SmsModel;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Sms\Domain\Model\SmsModel\MessageText;
use Sms\Domain\Model\SmsModel\PhoneNumber;
use Sms\Domain\Model\SmsModel\Sms;
use Sms\Domain\Model\SmsModel\SmsId;

class SmsTest extends TestCase
{
    private const MESSAGE = 'André Goutaire from Campus26 has just sent you a document to sign';
    private const PHONE_NUMBER = '+33123456789';
    private const TEST_ID = 'TestId';

    public function testSms(): void
    {
        $sms = FactorySmsBuilder::createSms(self::MESSAGE, [self::PHONE_NUMBER]);
        $this->assertEquals(self::MESSAGE, $sms->getSmsMessage());
        $this->assertEquals([self::PHONE_NUMBER], $sms->getSmsPhoneNumber());
        $this->assertStringStartsWith("sms_", $sms->getId());
    }

    public function testSmsRepository(): void
    {
        $smsWithId = FactorySmsBuilder::createSms(self::MESSAGE, [self::PHONE_NUMBER], new SmsId(self::TEST_ID));
        $this->assertEquals(self::TEST_ID, $smsWithId->getId());
        $this->assertInstanceOf(DateTimeImmutable::class, $smsWithId->getCreatedAt());
    }
}