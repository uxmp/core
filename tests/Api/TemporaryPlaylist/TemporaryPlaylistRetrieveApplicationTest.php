<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

class TemporaryPlaylistRetrieveApplicationTest extends MockeryTestCase
{
    private MockInterface $temporaryPlaylistRepository;

    private TemporaryPlaylistRetrieveApplication $subject;

    public function setUp(): void
    {
        $this->temporaryPlaylistRepository = Mockery::mock(TemporaryPlaylistRepositoryInterface::class);

        $this->subject = new TemporaryPlaylistRetrieveApplication(
            $this->temporaryPlaylistRepository,
        );
    }

    public function testRunReturnsResult(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $obj = Mockery::mock(TemporaryPlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $playlistId = 'some-playlist-id';

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->temporaryPlaylistRepository->shouldReceive('findOneBy')
            ->with(['owner' => $user])
            ->once()
            ->andReturn($obj);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode(['result' => $playlistId], JSON_PRETTY_PRINT))
            ->once();

        $obj->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($playlistId);

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                []
            )
        );
    }
}
