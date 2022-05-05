<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistDeletionApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private PlaylistDeletionApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);

        $this->subject = new PlaylistDeletionApplication(
            $this->playlistRepository,
        );
    }

    public function testRunDeletes(): void
    {
        $playlist = Mockery::mock(PlaylistInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $playlistId = 666;
        $userId = 42;

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER_ID)
            ->once()
            ->andReturn($userId);

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturn($playlist);
        $this->playlistRepository->shouldReceive('delete')
            ->with($playlist)
            ->once();

        $playlist->shouldReceive('getOwner->getId')
            ->withNoArgs()
            ->once()
            ->andReturn($userId);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(['result' => true], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }

    public function testRunDoesNotDeleteIfItemWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $playlistId = 666;

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(
                json_encode(['result' => false], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }
}
