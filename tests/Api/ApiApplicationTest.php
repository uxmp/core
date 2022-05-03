<?php

declare(strict_types=1);

namespace Uxmp\Core\Api;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Slim\App;
use Tuupola\Middleware\CorsMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Usox\HyperSonic\HyperSonicInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

class ApiApplicationTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $sessionValidatorMiddleware;

    private ApiApplication $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->sessionValidatorMiddleware = Mockery::mock(SessionValidatorMiddleware::class);

        $this->subject = new ApiApplication(
            $this->config,
            $this->sessionValidatorMiddleware
        );
    }

    public function testRunRuns(): void
    {
        $app = Mockery::mock(App::class);
        $logger = Mockery::mock(Logger::class);

        $basePath = 'some-base-path';
        $logFilePath = '/tmp';
        $logLevel = 666;
        $debugMode = true;

        $this->config->shouldReceive('getApiBasePath')
            ->withNoArgs()
            ->once()
            ->andReturn($basePath);
        $this->config->shouldReceive('getLogFilePath')
            ->withNoArgs()
            ->once()
            ->andReturn($logFilePath);
        $this->config->shouldReceive('getLogLevel')
            ->withNoArgs()
            ->once()
            ->andReturn($logLevel);
        $this->config->shouldReceive('getCookieName')
            ->withNoArgs()
            ->once()
            ->andReturn('some-cookie-name');
        $this->config->shouldReceive('getJwtSecret')
            ->withNoArgs()
            ->once()
            ->andReturn('some-jwt-secret');
        $this->config->shouldReceive('getCorsOrigin')
            ->withNoArgs()
            ->once()
            ->andReturn('some-cors-origin');
        $this->config->shouldReceive('getDebugMode')
            ->withNoArgs()
            ->once()
            ->andReturn($debugMode);

        $logger->shouldReceive('pushHandler')
            ->with(Mockery::type(RotatingFileHandler::class))
            ->once();

        $app->shouldReceive('addBodyParsingMiddleware')
            ->withNoArgs()
            ->once();
        $app->shouldReceive('addErrorMiddleware')
            ->with($debugMode, true, true, $logger)
            ->once();
        $app->shouldReceive('setBasePath')
            ->with($basePath)
            ->once();
        $app->shouldReceive('add')
            ->with($this->sessionValidatorMiddleware)
            ->once();
        $app->shouldReceive('add')
            ->with(Mockery::type(JwtAuthentication::class))
            ->once();
        $app->shouldReceive('add')
            ->with(Mockery::type(CorsMiddleware::class))
            ->once();

        // routes
        $app->shouldReceive('post')
            ->with('/common/login', Common\LoginApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/common/logout', Common\LogoutApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/play/history', Playback\PlaybackHistoryApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/play/mostplayed', Playback\MostPlayedApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/play/{id}', Playback\PlaySongApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/artists', Artist\ArtistListApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/artist/{artistId}', Artist\ArtistApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/artist/{artistId}/songs', Artist\ArtistSongsApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/albums/favorite', Album\AlbumFavoriteApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/albums/recent', Album\AlbumRecentApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/albums[/{artistId}]', Album\AlbumListApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/album/{albumId}', Album\AlbumApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/album/{albumId}/songs', Album\AlbumSongsApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/random/songs[/{limit}]', Random\RandomSongsApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/random/favorite[/{limit}]', Random\RandomFavoriteSongsApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/art/{type}/{id}', Art\ArtApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/user/favorite', User\FavoriteListApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/user/favorite/{type}/add', User\FavoriteAddApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/user/favorite/{type}/remove', User\FavoriteRemoveApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/radiostation', RadioStation\RadioStationCreationApplication::class)
            ->once();
        $app->shouldReceive('delete')
            ->with('/radiostation/{stationId}', RadioStation\RadioStationDeletionApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/radiostations', RadioStation\RadioStationListApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/radiostation/{stationId}', RadioStation\RadioStationRetrieveApplication::class)
            ->once();
        $app->shouldReceive('put')
            ->with('/radiostation/{stationId}', RadioStation\RadioStationEditApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/usersettings', User\UserSettingsRetrieveApplication::class)
            ->once();
        $app->shouldReceive('put')
            ->with('/usersettings', User\UserSettingsEditApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/usersettings/subsonic', User\SubSonic\SubSonicSettingsRetrieveApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/usersettings/subsonic', User\SubSonic\SubSonicSettingsCreateApplication::class)
            ->once();
        $app->shouldReceive('delete')
            ->with('/usersettings/subsonic', User\SubSonic\SubSonicSettingsDeleteApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/playlist', Playlist\PlaylistCreationApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/playlists', Playlist\PlaylistListApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/playlists/user', Playlist\PlaylistListByUserApplication::class)
            ->once();
        $app->shouldReceive('put')
            ->with('/playlist/{playlistId}', Playlist\PlaylistEditApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/playlist/{playlistId}', Playlist\PlaylistRetrieveApplication::class)
            ->once();
        $app->shouldReceive('delete')
            ->with('/playlist/{playlistId}', Playlist\PlaylistDeletionApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/playlist/{playlistId}/songs', Playlist\PlaylistAddMediaApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/playlist/{playlistId}/songs', Playlist\PlaylistSongListApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/playlist_types', PlaylistTypes\PlaylistTypesApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/rest/{methodName}', HyperSonicInterface::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/temporary_playlist', TemporaryPlaylist\TemporaryPlaylistRetrieveApplication::class)
            ->once();
        $app->shouldReceive('get')
            ->with('/temporary_playlist/{temporaryPlaylistId}/songs', TemporaryPlaylist\TemporaryPlaylistRetrieveSongsApplication::class)
            ->once();
        $app->shouldReceive('post')
            ->with('/temporary_playlist', TemporaryPlaylist\TemporaryPlaylistUpdateApplication::class)
            ->once();
        $app->shouldReceive('run')
            ->withNoArgs()
            ->once();

        $this->subject->run($app, $logger);
    }
}
