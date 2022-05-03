<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\App;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Usox\HyperSonic\HyperSonicInterface;
use Uxmp\Core\Api\Playback\MostPlayedApplication;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

/**
 * Registers all api routes
 */
final class ApiApplication
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly SessionValidatorMiddleware $sessionValidatorMiddleware,
    ) {
    }

    public function run(
        App $app,
        Logger $logger,
    ): void {
        $apiBasePath = $this->config->getApiBasePath();

        $rotating = new RotatingFileHandler(
            sprintf(
                '%s/router.log',
                $this->config->getLogFilePath(),
            ),
            0,
            $this->config->getLogLevel()
        );
        $logger->pushHandler($rotating);

        $app->addBodyParsingMiddleware();
        $app->addErrorMiddleware(
            $this->config->getDebugMode(),
            true,
            true,
            $logger
        );
        $app->setBasePath($apiBasePath);

        $app->add($this->sessionValidatorMiddleware);
        $app->add(new JwtAuthentication([
            'ignore' => [$apiBasePath . '/common/login', $apiBasePath . '/art', $apiBasePath . '/rest'],
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
        $app->get('/play/mostplayed', MostPlayedApplication::class);
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
        $app->get('/radiostations', RadioStation\RadioStationListApplication::class);
        $app->post('/radiostation', RadioStation\RadioStationCreationApplication::class);
        $app->delete('/radiostation/{stationId}', RadioStation\RadioStationDeletionApplication::class);
        $app->get('/radiostation/{stationId}', RadioStation\RadioStationRetrieveApplication::class);
        $app->put('/radiostation/{stationId}', RadioStation\RadioStationEditApplication::class);

        // user settings
        $app->get('/usersettings', User\UserSettingsRetrieveApplication::class);
        $app->put('/usersettings', User\UserSettingsEditApplication::class);
        $app->get('/usersettings/subsonic', User\SubSonic\SubSonicSettingsRetrieveApplication::class);
        $app->post('/usersettings/subsonic', User\SubSonic\SubSonicSettingsCreateApplication::class);
        $app->delete('/usersettings/subsonic', User\SubSonic\SubSonicSettingsDeleteApplication::class);

        // playlist
        $app->get('/playlists', Playlist\PlaylistListApplication::class);
        $app->get('/playlists/user', Playlist\PlaylistListByUserApplication::class);
        $app->post('/playlist', Playlist\PlaylistCreationApplication::class);
        $app->put('/playlist/{playlistId}', Playlist\PlaylistEditApplication::class);
        $app->get('/playlist/{playlistId}', Playlist\PlaylistRetrieveApplication::class);
        $app->delete('/playlist/{playlistId}', Playlist\PlaylistDeletionApplication::class);
        $app->post('/playlist/{playlistId}/songs', Playlist\PlaylistAddMediaApplication::class);
        $app->get('/playlist/{playlistId}/songs', Playlist\PlaylistSongListApplication::class);

        // temporary playlist
        $app->get('/temporary_playlist', TemporaryPlaylist\TemporaryPlaylistRetrieveApplication::class);
        $app->get('/temporary_playlist/{temporaryPlaylistId}/songs', TemporaryPlaylist\TemporaryPlaylistRetrieveSongsApplication::class);
        $app->post('/temporary_playlist', TemporaryPlaylist\TemporaryPlaylistUpdateApplication::class);

        // playlist types
        $app->get('/playlist_types', PlaylistTypes\PlaylistTypesApplication::class);

        // subsonic api
        $app->get('/rest/{methodName}', HyperSonicInterface::class);

        $app->run();
    }
}
