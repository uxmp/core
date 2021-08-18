<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Bootstrap\Init;

// replace with file to your own project bootstrap
require_once __DIR__ . '/vendor/autoload.php';

return Init::run(static function (ContainerInterface $dic) {
    return ConsoleRunner::createHelperSet($dic->get(EntityManagerInterface::class));
});
