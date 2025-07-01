<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Domain\Model\Sms\PhoneNumber;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\SmsId;

class SmsIdTest extends TestCase
{
    public function testShouldCreateWithGivenId(): void
    {
        $id = 'sms_123';
        $msg = 'test';

        $smsId = new SmsId($id);
        $sms = new Sms(new MessageText($msg), new MobilePhoneNumber("+33612345678"), $smsId);

        $this->assertSame($id, (string) $sms->getId());
        $this->assertSame($id, (string) $smsId);
    }


    public function testShouldGenerateIdIfNoneProvided(): void
    {
        $smsId = new SmsId();

        $this->assertNotEmpty((string) $smsId);
        $this->assertStringStartsWith('sms_', (string) $smsId);
    }

    public function testEqualsShouldReturnTrueForSameId(): void
    {
        $id = 'sms_123';
        $smsId1 = new SmsId($id);
        $smsId2 = new SmsId($id);

        $this->assertTrue($smsId1->equals($smsId2));
    }

    public function testEqualsShouldReturnFalseForDifferentId(): void
    {
        $smsId1 = new SmsId('sms_abc');
        $smsId2 = new SmsId('sms_xyz');

        $this->assertFalse($smsId1->equals($smsId2));
    }

    public function testShouldGenerateIdWithUniqidPrefixIfNoneProvided(): void
    {
        $smsId = new SmsId();

        $idStr = (string) $smsId;

        $this->assertNotEmpty($idStr, 'Generated ID should not be empty');
        $this->assertStringStartsWith('sms_', $idStr, 'Generated ID should start with "sms_"');
        $uniqidPart = substr($idStr, 4);

        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/i', $uniqidPart, 'uniqid part should be hexadecimal');

        $this->assertEquals(13, strlen($uniqidPart), 'uniqid part length should be 13');
    }
}
