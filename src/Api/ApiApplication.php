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

/**
 * Registers all api routes
 */
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
            'ignore' => [$apiBasePath . '/common/login', $apiBasePath . '/art'],
            'cookie' => $this->config->getCookieName(),
            'secret' => $this->config->getJwtSecret(),
            'logger' => $logger,
        ]));
        $app->add(new CorsMiddleware([
            'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            'headers.allow' => ['Authorization', 'Content-Type'],
            'origin' => [$this->config->getCorsOrigin(),],
            'logger' => $logger,
            'credentials' => true,
        ]));

        // common
        $app->post('/common/login', Common\LoginApplication::class);
        $app->post('/common/logout', Common\LogoutApplication::class);

        // playback
        $app->get('/play/history', Playback\PlaybackHistoryApplication::class);
        $app->get('/play/{id}', Playback\PlaySongApplication::class);

        // artists
        $app->get('/artists', Artist\ArtistListApplication::class);
        $app->get('/artist/{artistId}', Artist\ArtistApplication::class);
        $app->get('/artist/{artistId}/songs', Artist\ArtistSongsApplication::class);

        // albums
        $app->get('/albums/recent', Album\AlbumRecentApplication::class);
        $app->get('/albums/favorite', Album\AlbumFavoriteApplication::class);
        $app->get('/albums[/{artistId}]', Album\AlbumListApplication::class);
        $app->get('/album/{albumId}', Album\AlbumApplication::class);
        $app->get('/album/{albumId}/songs', Album\AlbumSongsApplication::class);

        // random
        $app->get('/random/songs[/{limit}]', Random\RandomSongsApplication::class);
        $app->get('/random/favorite[/{limit}]', Random\RandomFavoriteSongsApplication::class);

        // art
        $app->get('/art/{type}/{id}', Art\ArtApplication::class);

        // user favorites
        $app->get('/user/favorite', User\FavoriteListApplication::class);
        $app->post('/user/favorite/{type}/add', User\FavoriteAddApplication::class);
        $app->post('/user/favorite/{type}/remove', User\FavoriteRemoveApplication::class);

        // radio stations
        $app->post('/radiostation', RadioStation\RadioStationCreationApplication::class);
        $app->delete('/radiostation/{stationId}', RadioStation\RadioStationDeletionApplication::class);
        $app->get('/radiostations', RadioStation\RadioStationListApplication::class);
        $app->get('/radiostation/{stationId}', RadioStation\RadioStationRetrieveApplication::class);
        $app->put('/radiostation/{stationId}', RadioStation\RadioStationEditApplication::class);

        // user settings
        $app->get('/usersettings', User\UserSettingsRetrieveApplication::class);
        $app->put('/usersettings', User\UserSettingsEditApplication::class);

        // playlist
        $app->post('/playlist', Playlist\PlaylistCreationApplication::class);
        $app->get('/playlists', Playlist\PlaylistListApplication::class);

        $app->run();
    }
}
