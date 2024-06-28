<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;
use Sms\Domain\Model\Sms\MobilePhoneNumber;

use function SafePHP\strval;

class MobilePhoneNumberTest extends TestCase
{
    public function testValidPhoneNumbers(): void
    {
        $validNumbers = [
            '0601020304',
            '0701020304',
            '+33601020304',
            '+33799999999',
            '06 01 02 03 04'
        ];
        foreach ($validNumbers as $num) {
            $pn = new MobilePhoneNumber($num);
            $this->assertIsString($pn->getNumber());
        }
    }

    public function testInvalidPhoneNumbers(): void
    {
        $wrongNumbers = [
            '060-10/20',
            '060102030A',
            '123456789012345',
            '060011203a04',
            '+31212345678',
            '05 68 12 49 83',
        ];
        foreach ($wrongNumbers as $num) {
            try {
                new MobilePhoneNumber($num);
                $this->assertTrue(false);
            } catch (InvalidPhoneNumber $e) {
                $this->assertStringStartsWith('Invalid phone number ' . substr(strval($num), 0, 2), $e->getMessage());
            }
        }
    }

    public function testInvalidPhoneNumbersBadCharacter(): void
    {
        $emptyNumbers = [
            '      ',
            ''
        ];
        foreach ($emptyNumbers as $numEmpty) {
            try {
                new MobilePhoneNumber($numEmpty);
                $this->assertTrue(false);
            } catch (InvalidPhoneNumber $e) {
                $this->assertEquals('Bad and empty character in phone number', $e->getMessage());
            }
        }

        $wrongNumbers = [
            'abc',
            '06 70 82 73 76 89',
            '00 71 09 15 53'
        ];
        foreach ($wrongNumbers as $num) {
            try {
                new MobilePhoneNumber($num);
                $this->assertTrue(false);
            } catch (InvalidPhoneNumber $e) {
                $this->assertStringStartsWith('Invalid phone number', $e->getMessage());
            }
        }
    }
}
