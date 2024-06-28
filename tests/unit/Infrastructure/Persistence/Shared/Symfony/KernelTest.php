<?php

namespace Sms\Tests\Infrastructure\Shared\Symfony;

use PHPUnit\Framework\TestCase;
use Sms\Infrastructure\Shared\Symfony\Kernel as SymfonyKernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel as HttpKernelKernel;

class KernelTest extends TestCase
{
    private const BOOL = true;
    public function testConstruct(): void
    {
        $kernel = new SymfonyKernel("test kernel", self::BOOL);
        $this->assertInstanceOf(HttpKernelKernel::class, $kernel);
        $this->assertTrue($kernel->isDebug());
    }
}
