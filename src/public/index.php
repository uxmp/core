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
use Uxmp\Core\Api\Artist\ArtistListApplication;
use Uxmp\Core\Api\Playback\PlaySongApplication;
use Uxmp\Core\Api\Public\LoginApplication;
use Uxmp\Core\Api\Public\LogoutApplication;
use Uxmp\Core\Api\Random\RandomSongsApplication;
use Uxmp\Core\Bootstrap\Init;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;

require __DIR__ . '/../../vendor/autoload.php';

Init::run(static function (ContainerInterface $dic): void {
    /** @var ConfigProviderInterface $config */
    $config = $dic->get(ConfigProviderInterface::class);

    $app = AppFactory::createFromContainer($dic);
    $app->addBodyParsingMiddleware();
    $app->addErrorMiddleware(true, true, true);

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
        'ignore' => ['/public/login', '/art'],
        'cookie' => $config->getCookieName(),
        'secret' => $config->getJwtSecret(),
        'logger' => $logger,
    ]));
    $app->add(new CorsMiddleware([
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'origin' => ['*'],
        'logger' => $logger,
        'credentials' => true,
    ]));

    $app->post('/public/login', LoginApplication::class);
    $app->get('/public/logout', LogoutApplication::class);
    $app->get('/play/{id}', PlaySongApplication::class);
    $app->get('/artists', ArtistListApplication::class);
    $app->get('/albums', AlbumListApplication::class);
    $app->get('/art/{type}/{id}', ArtApplication::class);
    $app->get('/album/{albumId}', AlbumApplication::class);
    $app->get('/random/songs', RandomSongsApplication::class);

    $app->run();
});
