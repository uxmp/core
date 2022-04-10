<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use JsonSerializable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class MostPlayedApplicationTest extends MockeryTestCase
{
    private MockInterface $playbackHistoryRepository;

    private MockInterface $songRepository;

    private MockInterface $resultItemFactory;

    private MostPlayedApplication $subject;

    public function setUp(): void
    {
        $this->playbackHistoryRepository = Mockery::mock(PlaybackHistoryRepositoryInterface::class);
        $this->songRepository = Mockery::mock(SongRepositoryInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new MostPlayedApplication(
            $this->playbackHistoryRepository,
            $this->songRepository,
            $this->resultItemFactory,
        );
    }

    public function testRunReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $songItem = Mockery::mock(JsonSerializable::class);
        $stream = Mockery::mock(StreamInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

        $songId = 666;
        $missingSongId = 33;
        $count = 42;
        $resultData = ['some-result'];

        $song->shouldReceive('getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);

        $this->playbackHistoryRepository->shouldReceive('getMostPlayed')
            ->withNoArgs()
            ->once()
            ->andReturn([['cnt' => $count, 'song_id' => $songId], ['cnt' => 222, 'song_id' => $missingSongId]]);

        $this->songRepository->shouldReceive('find')
            ->with($songId)
            ->once()
            ->andReturn($song);
        $this->songRepository->shouldReceive('find')
            ->with($missingSongId)
            ->once()
            ->andReturnNull();

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($songItem);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(json_encode(['items' => [['count' => $count, 'song' => $resultData]]], JSON_PRETTY_PRINT))
            ->once();

        $songItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($resultData);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
