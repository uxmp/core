<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\AccessKeyInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;

class SubSonicSettingsCreateApplicationTest extends MockeryTestCase
{
    private Mockery\MockInterface $accessKeyRepository;

    private SubSonicSettingsCreateApplication $subject;

    public function setUp(): void
    {
        $this->accessKeyRepository = Mockery::mock(AccessKeyRepositoryInterface::class);

        $this->subject = new SubSonicSettingsCreateApplication(
            $this->accessKeyRepository,
        );
    }

    public function testRunRetrievesAlreadyExistingSettings(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $accessToken = 'some-token';

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['accessToken' => $accessToken], JSON_PRETTY_PRINT))
            ->once();

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessTokenEnum::TYPE_SUBSONIC,
            ])
            ->once()
            ->andReturn($accessKey);

        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([AccessTokenEnum::CONFIG_KEY_TOKEN => $accessToken]);

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'accessToken' => $accessToken,
                ]
            )
        );
    }

    public function testRunRetrievesCreatedSettings(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $accessToken = 'some-token';

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['accessToken' => $accessToken], JSON_PRETTY_PRINT))
            ->once();

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->accessKeyRepository->shouldReceive('findOneBy')
            ->with([
                'user' => $user,
                'type_id' => AccessTokenEnum::TYPE_SUBSONIC,
            ])
            ->once()
            ->andReturnNull();
        $this->accessKeyRepository->shouldReceive('prototype')
            ->withNoArgs()
            ->once()
            ->andReturn($accessKey);
        $this->accessKeyRepository->shouldReceive('save')
            ->with($accessKey)
            ->once();

        $accessKey->shouldReceive('setUser')
            ->with($user)
            ->once()
            ->andReturnSelf();
        $accessKey->shouldReceive('setActive')
            ->with(true)
            ->once()
            ->andReturnSelf();
        $accessKey->shouldReceive('setTypeId')
            ->with(AccessTokenEnum::TYPE_SUBSONIC)
            ->once()
            ->andReturnSelf();
        $accessKey->shouldReceive('setConfig')
            ->with(Mockery::on(function (array $config): bool {
                return strlen($config[AccessTokenEnum::CONFIG_KEY_TOKEN]) === AccessTokenEnum::SUBSONIC_KEY_LENGTH;
            }))
            ->once()
            ->andReturnSelf();
        $accessKey->shouldReceive('getConfig')
            ->withNoArgs()
            ->once()
            ->andReturn([AccessTokenEnum::CONFIG_KEY_TOKEN => $accessToken]);

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'accessToken' => $accessToken,
                ]
            )
        );
    }
}
