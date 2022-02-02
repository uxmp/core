<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\App;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;

final class ApiApplication
{
    public function __construct(
        private ConfigProviderInterface $config,
        private SessionValidatorMiddleware $sessionValidatorMiddleware,
    ) {
    }

    public function run(
        App $app,
        Logger $logger,
    ): void {
        $apiBasePath = $this->config->getApiBasePath();

        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(true, true, true);
        $app->setBasePath($apiBasePath);

        $rotating = new RotatingFileHandler(
            sprintf(
                '%s/router.log',
                $this->config->getLogFilePath(),
            ),
            0,
            $this->config->getLogLevel()
        );
        $logger->pushHandler($rotating);

        $app->add($this->sessionValidatorMiddleware);
        $app->add(new JwtAuthentication([
            'ignore' => [$apiBasePath . '/common', $apiBasePath . '/art'],
            'cookie' => $this->config->getCookieName(),
            'secret' => $this->config->getJwtSecret(),
            'logger' => $logger,
        ]));
        $app->add(new CorsMiddleware([
            'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'headers.allow' => ['Authorization', 'Content-Type'],
            'origin' => [$this->config->getCorsOrigin()],
            'logger' => $logger,
            'credentials' => true
        ]));

        $app->post('/common/login', Common\LoginApplication::class);
        $app->post('/common/logout', Common\LogoutApplication::class);
        $app->get('/play/history', Playback\PlaybackHistoryApplication::class);
        $app->get('/play/{id}', Playback\PlaySongApplication::class);
        $app->get('/artists', Artist\ArtistListApplication::class);
        $app->get('/artist/{artistId}', Artist\ArtistApplication::class);
        $app->get('/artist/{artistId}/songs', Artist\ArtistSongsApplication::class);
        $app->get('/albums/recent', Album\AlbumRecentApplication::class);
        $app->get('/albums/favorite', Album\AlbumFavoriteApplication::class);
        $app->get('/albums[/{artistId}]', Album\AlbumListApplication::class);
        $app->get('/album/{albumId}', Album\AlbumApplication::class);
        $app->get('/album/{albumId}/songs', Album\AlbumSongsApplication::class);
        $app->get('/random/songs[/{limit}]', Random\RandomSongsApplication::class);
        $app->get('/random/favorite[/{limit}]', Random\RandomFavoriteSongsApplication::class);
        $app->get('/art/{type}/{id}', Art\ArtApplication::class);
        $app->get('/user/favorite', User\FavoriteListApplication::class);
        $app->post('/user/favorite/{type}/add', User\FavoriteAddApplication::class);
        $app->post('/user/favorite/{type}/remove', User\FavoriteRemoveApplication::class);
        $app->post('/radiostation', RadioStation\RadioStationCreationApplication::class);
        $app->delete('/radiostation/{stationId}', RadioStation\RadioStationDeletionApplication::class);
        $app->get('/radiostations', RadioStation\RadioStationListApplication::class);
        $app->get('/radiostation/{stationId}', RadioStation\RadioStationRetrieveApplication::class);
        $app->put('/radiostation/{stationId}', RadioStation\RadioStationEditApplication::class);

        $app->run();
    }
}
