<?php

namespace Sms\Tests\Domain\Model\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Domain\Model\Sms\MessageText;

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
