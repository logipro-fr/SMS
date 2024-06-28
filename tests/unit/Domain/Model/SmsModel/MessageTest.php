<?php

namespace Sms\Domain\Model\SmsModel;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\SmsModel\MessageText;

class MessageTest extends TestCase
{
    private const MESSAGE = 'André Goutaire from Campus26 has just sent you a document to sign';
    public function testMessage(): void
    {
        $message = new MessageText(self::MESSAGE);
        $this->assertEquals(self::MESSAGE, $message->getMessagetext());
        $this->assertInstanceOf(MessageText::class, $message);
    }
}
