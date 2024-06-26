<?php

namespace Sms\Domain\Model\SmsModel;

class PhoneNumber
{
    /**
    * @param array<string> $phoneNumber
    **/
    public function __construct(private array $phoneNumber)
    {
    }
     /**
     * @return array<string>
     */
    public function getPhoneNumber(): array
    {
        return $this->phoneNumber;
    }

    public function __toString()
    {
        return strval($this->phoneNumber[0]);
    }
}
