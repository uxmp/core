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
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;

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

        $logger->shouldReceive('pushHandler')
            ->with(Mockery::type(RotatingFileHandler::class))
            ->once();

        $app->shouldReceive('addBodyParsingMiddleware')
            ->withNoArgs()
            ->once();
        $app->shouldReceive('addErrorMiddleware')
            ->with(true, true, true)
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
            ->with('/art/{type}/{id}', Art\ArtApplication::class)
            ->once();
        $app->shouldReceive('run')
            ->withNoArgs()
            ->once();

        $this->subject->run($app, $logger);
    }
}
