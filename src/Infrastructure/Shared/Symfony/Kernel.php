<?php

namespace Sms\Infrastructure\Shared\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const BOOL = true;

    public function __construct(string $environment, bool $debug = self::BOOL)
    {
        parent::__construct($environment, $debug);
    }
}
