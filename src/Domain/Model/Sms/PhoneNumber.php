<?php

namespace Sms\Domain\Model\Sms;

use Sms\Domain\Model\Sms\Exceptions\InvalidPhoneNumber;

class PhoneNumber
{
    public const PATTERN_PHONE_NUMBER = '/^((\+|00)33|0)([1-9]\d{8})$/';
    public const PATTERN_INTERNATIONAL_PHONE_NUMBER = '/^(\+33)[1-9]\d{8}$/';
    public const THIRD_CAPTURE_GROUP_OF_REGEXP_PATTERN = '$3';
    public const SPACE_PATTERN = '/\s/';

    protected string $number;
    public function __construct(string $number)
    {
        $number = $this->normalizeFormat($number);
        $this->checkPhoneNumberFormat($number);
        $this->number = $number;
    }

    protected function normalizeFormat(string $number): string
    {
        /** @var string $number */
        $number = preg_replace([self::SPACE_PATTERN], [''], $number) ?? '';
        if ($number == '') {
            throw new InvalidPhoneNumber('Bad and empty character in phone number');
        }
        return $number;
    }

    private function checkPhoneNumberFormat(string $number): void
    {
        if (!preg_match(self::PATTERN_PHONE_NUMBER, $number)) {
            $escapedPhoneNumber = htmlspecialchars($number, ENT_QUOTES, 'UTF-8');
            throw new InvalidPhoneNumber('Invalid phone number ' . $escapedPhoneNumber);
        }
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Formate le numéro de téléphone
     * au format E164 "+_indicatifInternational_numéro"
     */
    public function getInternationalFormatedNumber(): string
    {
        $formatedNumber = preg_replace(
            self::PATTERN_PHONE_NUMBER,
            "+33" . self::THIRD_CAPTURE_GROUP_OF_REGEXP_PATTERN,
            $this->number
        );
        return $formatedNumber ?? '';
    }
}
