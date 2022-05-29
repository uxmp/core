<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

class TemporaryPlaylistRetrieveSongsApplicationTest extends MockeryTestCase
{
    private MockInterface $temporaryPlaylistRepository;

    private MockInterface $songRepository;

    private MockInterface $resultItemFactory;

    private TemporaryPlaylistRetrieveSongsApplication $subject;

    public function setUp(): void
    {
        $this->temporaryPlaylistRepository = Mockery::mock(TemporaryPlaylistRepositoryInterface::class);
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new TemporaryPlaylistRetrieveSongsApplication(
            $this->temporaryPlaylistRepository,
            $this->songRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsResult(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $temporaryPlaylist = Mockery::mock(TemporaryPlaylistInterface::class);
        $user = Mockery::mock(UserInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $songListItem = Mockery::mock(JsonSerializable::class);

        $playlistId = 'some-playlist-id';
        $songId1 = 666;
        $songId2 = 42;
        $songListResult = 'some-result';
        $offset = 42;

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->temporaryPlaylistRepository->shouldReceive('findOneBy')
            ->with(['owner' => $user, 'id' => $playlistId])
            ->once()
            ->andReturn($temporaryPlaylist);

        $temporaryPlaylist->shouldReceive('getSongList')
            ->withNoArgs()
            ->once()
            ->andReturn([$songId1, $songId2,]);
        $temporaryPlaylist->shouldReceive('getOffset')
            ->withNoArgs()
            ->once()
            ->andReturn($offset);

        $this->songRepository->shouldReceive('find')
            ->with($songId1)
            ->once()
            ->andReturn($song);
        $this->songRepository->shouldReceive('find')
            ->with($songId2)
            ->once()
            ->andReturnNull();

        $song->shouldReceive('getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($songListItem);

        $songListItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($songListResult);

        $stream->shouldReceive('write')
            ->with(json_encode(['offset' => $offset, 'songs' => [$songListResult]], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                ['temporaryPlaylistId' => $playlistId]
            )
        );
    }

    public function testRunReturnsNotFoundResultIfNotExisting(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $user = Mockery::mock(UserInterface::class);

        $playlistId = 'some-playlist-id';

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->temporaryPlaylistRepository->shouldReceive('findOneBy')
            ->with(['owner' => $user, 'id' => $playlistId])
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                ['temporaryPlaylistId' => $playlistId]
            )
        );
    }
}
