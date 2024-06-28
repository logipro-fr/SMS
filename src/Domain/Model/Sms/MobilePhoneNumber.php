<?php

namespace Sms\Domain\Model\Sms;

use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class MobilePhoneNumber extends PhoneNumber
{
    public const PATTERN_MOBILE_PHONE_NUMBER = '/^((\+|00)33|0)([67]\d{8}$)/';
    public const PATTERN_INTERNATIONAL_MOBILE_PHONE_NUMBER = '/^(\+33)[67]\d{8}$/';

    public function __construct(string $number)
    {
        $number = $this->normalizeFormat($number);
        $this->checkPhoneNumberFormat($number);
        $this->number = $number;
    }

    private function checkPhoneNumberFormat(string $number): void
    {
        if (!preg_match(self::PATTERN_MOBILE_PHONE_NUMBER, $number)) {
            $escapedPhoneNumber = htmlspecialchars($number, ENT_QUOTES, 'UTF-8');
            throw new InvalidPhoneNumber('Invalid phone number ' . $escapedPhoneNumber);
        }
    }
}
