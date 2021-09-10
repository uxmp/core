<?php

declare(strict_types=1);

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Uxmp\Core\Api\Album\AlbumApplication;
use Uxmp\Core\Api\Album\AlbumListApplication;
use Uxmp\Core\Api\Art\ArtApplication;
use Uxmp\Core\Api\Artist\ArtistApplication;
use Uxmp\Core\Api\Artist\ArtistListApplication;
use Uxmp\Core\Api\Playback\PlaySongApplication;
use Uxmp\Core\Api\Common\LoginApplication;
use Uxmp\Core\Api\Common\LogoutApplication;
use Uxmp\Core\Api\Random\RandomSongsApplication;
use Uxmp\Core\Bootstrap\Init;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;

require __DIR__ . '/../../vendor/autoload.php';

Init::run(static function (ContainerInterface $dic): void {
    /** @var ConfigProviderInterface $config */
    $config = $dic->get(ConfigProviderInterface::class);

    $apiBasePath = $config->getApiBasePath();

    $app = AppFactory::createFromContainer($dic);
    $app->addBodyParsingMiddleware();
    $app->addErrorMiddleware(true, true, true);
    $app->setBasePath($apiBasePath);

    $logger = new Logger('slim');
    $rotating = new RotatingFileHandler(
        sprintf(
            '%s/router.log',
            $config->getLogFilePath(),
        ),
        0,
        $config->getLogLevel()
    );
    $logger->pushHandler($rotating);

    $app->add($dic->get(SessionValidatorMiddleware::class));
    $app->add(new JwtAuthentication([
        'ignore' => [$apiBasePath . '/common', $apiBasePath . '/art'],
        'cookie' => $config->getCookieName(),
        'secret' => $config->getJwtSecret(),
        'logger' => $logger,
    ]));
    $app->add(new CorsMiddleware([
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'headers.allow' => ['Authorization', 'Content-Type'],
        'origin' => [$config->getCorsOrigin()],
        'logger' => $logger,
        'credentials' => true
    ]));

    $app->post('/common/login', LoginApplication::class);
    $app->post('/common/logout', LogoutApplication::class);
    $app->get('/play/{id}', PlaySongApplication::class);
    $app->get('/artists', ArtistListApplication::class);
    $app->get('/artist/{artistId}', ArtistApplication::class);
    $app->get('/albums[/{artistId}]', AlbumListApplication::class);
    $app->get('/art/{type}/{id}', ArtApplication::class);
    $app->get('/album/{albumId}', AlbumApplication::class);
    $app->get('/random/songs[/{limit}]', RandomSongsApplication::class);

    $app->run();
});
