<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Generator;
use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Component\Playlist\PlaylistSongRetrieverInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

class PlaylistSongListApplicationTest extends MockeryTestCase
{
    private MockInterface $playlistRepository;

    private MockInterface $playlistSongRetriever;

    private MockInterface $resultItemFactory;

    private PlaylistSongListApplication $subject;

    public function setUp(): void
    {
        $this->playlistRepository = Mockery::mock(PlaylistRepositoryInterface::class);
        $this->playlistSongRetriever = Mockery::mock(PlaylistSongRetrieverInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new PlaylistSongListApplication(
            $this->playlistRepository,
            $this->playlistSongRetriever,
            $this->resultItemFactory,
        );
    }

    public function testRunErrorsIfPlaylistWasNotFound(): void
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

    public function testRunEdits(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $playlist = Mockery::mock(PlaylistInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $songListItem = Mockery::mock(JsonSerializable::class);
        $user = Mockery::mock(UserInterface::class);

        $playlistId = 666;
        $result = 'some-result';

        $this->playlistRepository->shouldReceive('find')
            ->with($playlistId)
            ->once()
            ->andReturn($playlist);

        $generator = function (mixed $item): Generator {
            yield $item;
        };

        $this->playlistSongRetriever->shouldReceive('retrieve')
            ->with($playlist, $user)
            ->once()
            ->andReturn($generator($song));

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($songListItem);

        $song->shouldReceive('getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);

        $songListItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $stream->shouldReceive('write')
            ->with(
                json_encode(['items' => [$result]], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['playlistId' => (string) $playlistId])
        );
    }
}
