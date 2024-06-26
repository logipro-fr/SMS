<?php

namespace Sms\Infrastructure\Shared\Symfony;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Dotenv\Dotenv;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $dotenvPath = __DIR__ . '/.env';

        $dotenv = new Dotenv();
        $dotenv->load($dotenvPath);

        $envLocalPath = __DIR__ . '/.env.local';
        if (file_exists($envLocalPath)) {
            $dotenv->load($envLocalPath);
        }
    }
}
