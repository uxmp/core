<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Uxmp\Core\Api\ApiApplication;
use Uxmp\Core\Bootstrap\Init;

require __DIR__ . '/../../vendor/autoload.php';

Init::run(static function (ContainerInterface $dic): void {
    $dic->get(ApiApplication::class)->run(
        AppFactory::createFromContainer($dic),
        new Logger('api')
    );
});
