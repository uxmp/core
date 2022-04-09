<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Orm\Model\RadioStationInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistRetrieveApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private PlaylistRetrieveApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);

        $this->subject = new PlaylistRetrieveApplication(
            $this->playlistRepository,
        );
    }

    public function testRunReturnsNotFoundIfStatioWasNotFound(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $this->playlistRepository->shouldReceive('find')
            ->with(0)
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunReturnsStation(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(RadioStationInterface::class);

        $playlistId = 666;
        $name = 'some-name';

        $result = [
            'id' => $playlistId,
            'name' => $name,
        ];

        $playlist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($playlistId);
        $playlist->shouldReceive('getName')
            ->withNoArgs()
            ->once()
            ->andReturn($name);

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturn($playlist);

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
                json_encode($result, JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }
}
