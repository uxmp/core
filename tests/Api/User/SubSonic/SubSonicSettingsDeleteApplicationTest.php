<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User\SubSonic;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Authentication\AccessKey\AccessTokenEnum;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\AccessKeyInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AccessKeyRepositoryInterface;

class SubSonicSettingsDeleteApplicationTest extends MockeryTestCase
{
    private Mockery\MockInterface $accessKeyRepository;

    private SubSonicSettingsDeleteApplication $subject;

    public function setUp(): void
    {
        $this->accessKeyRepository = Mockery::mock(AccessKeyRepositoryInterface::class);

        $this->subject = new SubSonicSettingsDeleteApplication(
            $this->accessKeyRepository,
        );
    }

    public function testRunDeletesAndRetrievesEmptySettings(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $accessKey = Mockery::mock(AccessKeyInterface::class);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['accessToken' => null], JSON_PRETTY_PRINT))
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
        $this->accessKeyRepository->shouldReceive('delete')
            ->with($accessKey)
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                [
                    'accessToken' => null,
                ]
            )
        );
    }
}
