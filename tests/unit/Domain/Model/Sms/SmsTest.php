<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\SmsId;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\Sms;

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

    public function testCreatedAtIsSet(): void
    {
        $message = new MessageText(self::MESSAGE);
        $phoneNumber = new MobilePhoneNumber(self::PHONE_NUMBER);

        $now = new \DateTimeImmutable();
        $factory = new FactorySmsBuilder();
        $sms = $factory->createSms($message, $phoneNumber);

        $this->assertInstanceOf(\DateTimeImmutable::class, $sms->getCreatedAt());
        $this->assertGreaterThanOrEqual($now->getTimestamp(), $sms->getCreatedAt()->getTimestamp());
    }

    public function testGetIdReturnsExpectedValue(): void
    {
        $expectedId = 'sms_test_456';
        $smsId = new SmsId($expectedId);

        $this->assertSame($expectedId, $smsId->getId());
    }

    public function testCreatedAt(): void
    {
        $message = new MessageText(self::MESSAGE);
        $phoneNumber = new MobilePhoneNumber(self::PHONE_NUMBER);
        $customDate = new \DateTimeImmutable('2023-01-01 12:00:00');

        $sms = new Sms($message, $phoneNumber, new SmsId('sms_id_test'), $customDate);
        $this->assertSame($customDate, $sms->getCreatedAt());
    }
}
