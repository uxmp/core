<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Bootstrap\Init;

include_once __DIR__ . '/../vendor/autoload.php';

return Init::run(static function (ContainerInterface $dic): ObjectManager {
    return $dic->get(EntityManagerInterface::class);
});
