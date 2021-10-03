<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Component\Session\JwtManagerInterface;
use Uxmp\Core\Component\Session\SessionManagerInterface;
use Uxmp\Core\Orm\Model\SessionInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class LoginApplicationTest extends MockeryTestCase
{
    private MockInterface $jwtManager;

    private MockInterface $configProvider;

    private MockInterface $sessionManager;

    private LoginApplication $subject;

    public function setUp(): void
    {
        $this->jwtManager = \Mockery::mock(JwtManagerInterface::class);
        $this->configProvider = \Mockery::mock(ConfigProviderInterface::class);
        $this->sessionManager = \Mockery::mock(SessionManagerInterface::class);

        $this->subject = new LoginApplication(
            $this->jwtManager,
            $this->configProvider,
            $this->sessionManager
        );
    }

    public function testRunThrowsExceptionIfLoginFails(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(
                ['data' => ['msg' => 'Username or password wrong']],
                JSON_PRETTY_PRINT
            ))
            ->once();

        $request->shouldReceive('getParsedBody')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->sessionManager->shouldReceive('login')
            ->with('', '')
            ->once()
            ->andReturnNull();

        call_user_func($this->subject, $request, $response, []);
    }

    public function testRunLogsInAndReturnsResponse(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $user = \Mockery::mock(UserInterface::class);
        $session = \Mockery::mock(SessionInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $lifetime = 123456;
        $userId = 666;
        $userName = 'some-name';
        $cookieName = 'some-cookie-name';
        $apiBasePath = 'some-api-base-path';
        $token = 'some-token';
        $sessionId = 42;

        $request->shouldReceive('getParsedBody')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->sessionManager->shouldReceive('login')
            ->with('', '')
            ->once()
            ->andReturn($session);

        $this->configProvider->shouldReceive('getTokenLifetime')
            ->withNoArgs()
            ->once()
            ->andReturn($lifetime);
        $this->configProvider->shouldReceive('getCookieName')
            ->withNoArgs()
            ->once()
            ->andReturn($cookieName);
        $this->configProvider->shouldReceive('getApiBasePath')
            ->withNoArgs()
            ->once()
            ->andReturn($apiBasePath);

        $session->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($sessionId);
        $session->shouldReceive('getUser')
            ->withNoArgs()
            ->once()
            ->andReturn($user);

        $user->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);
        $user->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($userName);

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
                \Mockery::on(function (string $value) use ($token, $cookieName, $apiBasePath): bool {
                    $expect = sprintf(
                        '%s=%s; path=%s/play; Expires=',
                        $cookieName,
                        $token,
                        $apiBasePath
                    );

                    return str_starts_with($value, $expect);
                })
            )
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(
                    [
                        'data' => [
                            'token' => $token,
                            'user' => ['id' => $userId, 'name' => $userName]
                        ]
                    ],
                    JSON_PRETTY_PRINT
                )
            )
            ->once();

        $this->jwtManager->shouldReceive('encode')
            ->with(\Mockery::on(function (array $value) use ($lifetime, $sessionId): bool {
                return $value['iat'] <= time() &&
                    $value['exp'] <= time() + $lifetime &&
                    $value['sub'] == (string) $sessionId;
            }))
            ->once()
            ->andReturn($token);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
