<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Common;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Component\Authentication\JwtManagerInterface;
use Uxmp\Core\Component\Authentication\SessionManagerInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\SessionInterface;
use Uxmp\Core\Orm\Model\UserInterface;

class LoginApplicationTest extends MockeryTestCase
{
    private MockInterface $jwtManager;

    private MockInterface $configProvider;

    private MockInterface $sessionManager;

    private MockInterface $schemaValidator;

    private LoginApplication $subject;

    public function setUp(): void
    {
        $this->jwtManager = Mockery::mock(JwtManagerInterface::class);
        $this->configProvider = Mockery::mock(ConfigProviderInterface::class);
        $this->sessionManager = Mockery::mock(SessionManagerInterface::class);
        $this->schemaValidator = Mockery::mock(SchemaValidatorInterface::class);

        $this->subject = new LoginApplication(
            $this->jwtManager,
            $this->configProvider,
            $this->sessionManager,
            $this->schemaValidator,
        );
    }

    public function testRunThrowsExceptionIfLoginFails(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

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

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with($request, 'Login.json')
            ->once()
            ->andReturn(['username' => '', 'password' => '']);

        $this->sessionManager->shouldReceive('login')
            ->with('', '')
            ->once()
            ->andReturnNull();

        call_user_func($this->subject, $request, $response, []);
    }

    public function testRunLogsInAndReturnsResponse(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $session = Mockery::mock(SessionInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $lifetime = 123456;
        $userId = 666;
        $userName = 'some-name';
        $cookieName = 'some-cookie-name';
        $apiBasePath = 'some-api-base-path';
        $token = 'some-token';
        $sessionId = 42;
        $loginUser = 'some-user';
        $loginPassword = 'some-password';
        $language = 'some-language';

        $this->schemaValidator->shouldReceive('getValidatedBody')
            ->with($request, 'Login.json')
            ->once()
            ->andReturn(['username' => $loginUser, 'password' => $loginPassword]);

        $this->sessionManager->shouldReceive('login')
            ->with($loginUser, $loginPassword)
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
        $user->shouldReceive('getLanguage')
            ->withNoArgs()
            ->once()
            ->andReturn($language);

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
                Mockery::on(function (string $value) use ($token, $cookieName, $apiBasePath): bool {
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
                            'user' => ['id' => $userId, 'name' => $userName, 'language' => $language],
                        ],
                    ],
                    JSON_PRETTY_PRINT
                )
            )
            ->once();

        $this->jwtManager->shouldReceive('encode')
            ->with(
                Mockery::on(function (array $value) use ($lifetime, $sessionId): bool {
                    return $value['iat'] <= time() &&
                    $value['exp'] <= time() + $lifetime &&
                    $value['sub'] == (string) $sessionId;
                })
            )
            ->once()
            ->andReturn($token);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
