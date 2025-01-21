<?php

use Sms\Infrastructure\Shared\Symfony\Kernel;

use function SafePHP\strval;

require_once dirname(__DIR__, 2) . '/vendor/autoload_runtime.php';

/** @param array<string,string|bool> $context */
return function (array $context) {
    //$kernelClass = $_ENV['KERNEL_CLASS'];
    return new Kernel(strval($context['APP_ENV']), (bool) $context['APP_DEBUG']);
};
