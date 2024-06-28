<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;
use Sms\Domain\Model\Sms\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function testPhoneNumbers(): void
    {
        $bonNumeros = [
            '0601020304',
            '0701020304',
            '+33601020304',
            '+33799999999',
            '06 01 02 03 04'
        ];
        for ($i = 0; $i < count($bonNumeros); $i++) {
            $pn = new PhoneNumber($bonNumeros[$i]);
            $this->assertIsString($pn->getNumber());
        }
    }

    public function testInvalidPhoneNumbers(): void
    {
        $mauvaisNumeros = [
            '0601020',
            '060102030A',
            '123456789012345',
            '060011203a04',
            '007101020304'
        ];
        for ($i = 0; $i < count($mauvaisNumeros); $i++) {
            try {
                new PhoneNumber($mauvaisNumeros[$i]);
                $this->assertSame('Ce numero est correct', $mauvaisNumeros[$i]);
            } catch (InvalidPhoneNumber $e) {
                $this->assertStringStartsWith(
                    'Invalid phone number ' . substr(strval($mauvaisNumeros[$i]), 0, 2),
                    $e->getMessage()
                );
            }
        }
    }

    public function testInvalidPhoneNumbersBadCharacter(): void
    {
        $mauvaisNumeros = [
            '      '
        ];
        for ($i = 0; $i < count($mauvaisNumeros); $i++) {
            try {
                new PhoneNumber($mauvaisNumeros[$i]);
                $this->assertSame('Ce numero est correct', $mauvaisNumeros[$i]);
            } catch (InvalidPhoneNumber $e) {
                $this->assertInstanceOf(InvalidPhoneNumber::class, $e);
                $this->assertStringContainsString('Bad and empty character in phone number', $e->getMessage());
            }
        }
        $mauvaisNumeros = [
            'abc',
            '06 70 82 73 76 89',
            '00 71 09 15 53'
        ];
        for ($i = 0; $i < count($mauvaisNumeros); $i++) {
            try {
                new PhoneNumber($mauvaisNumeros[$i]);
                $this->assertSame('Ce numero est correct', $mauvaisNumeros[$i]);
            } catch (InvalidPhoneNumber $e) {
                $this->assertInstanceOf(InvalidPhoneNumber::class, $e);
                $this->assertStringContainsString('Invalid phone number', $e->getMessage());
            }
        }
    }

    public function testGetInternationalPhoneNumbers(): void
    {
        $correctNumbers = [
            '0201020304',
            '0401020304',
            '0033601020304',
            '+33899999999',
            '09 01 02 03 04'
        ];

        foreach ($correctNumbers as $number) {
            $pn = new PhoneNumber($number);
            $this->assertMatchesRegularExpression(
                PhoneNumber::PATTERN_INTERNATIONAL_PHONE_NUMBER,
                $pn->getInternationalFormatedNumber()
            );
        }
    }
}
