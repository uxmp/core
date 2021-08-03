<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Usox\Core\Api\Album\AlbumListApplication;
use Usox\Core\Api\Playback\PlaySongApplication;
use Usox\Core\Bootstrap\Init;

require __DIR__ . '/../../vendor/autoload.php';

Init::run(static function (ContainerInterface $dic): void {
    $app = AppFactory::createFromContainer($dic);

    $app->get('/play/{id}', PlaySongApplication::class);
    $app->get('/albums', AlbumListApplication::class);

    $app->run();
});
