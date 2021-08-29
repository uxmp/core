<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\SessionManagerInterface;

class LogoutApplicationTest extends MockeryTestCase
{
    private MockInterface $sessionManager;

    private MockInterface $configProvider;

    private LogoutApplication $subject;

    public function setUp(): void
    {
        $this->sessionManager = Mockery::mock(SessionManagerInterface::class);
        $this->configProvider = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new LogoutApplication(
            $this->sessionManager,
            $this->configProvider
        );
    }

    public function testRunPerformsLogout(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $sessionId = 666;
        $cookieName = 'some-cookie-name';
        $apiBasePath = 'some-api-path';

        $request->shouldReceive('getAttribute')
            ->with('sessionId')
            ->once()
            ->andReturn((string) $sessionId);

        $this->sessionManager->shouldReceive('logout')
            ->with($sessionId)
            ->once();

        $this->configProvider->shouldReceive('getCookieName')
            ->withNoArgs()
            ->once()
            ->andReturn($cookieName);
        $this->configProvider->shouldReceive('getApiBasePath')
            ->withNoArgs()
            ->once()
            ->andReturn($apiBasePath);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with(
                'Set-Cookie',
                Mockery::on(function ($value) use ($cookieName, $apiBasePath) {
                    return str_starts_with(
                        $value,
                        sprintf(
                            '%s=; path=%splay; Expires=',
                            $cookieName,
                            $apiBasePath
                        )
                    );
                })
            )
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(json_encode(['items' => true], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
