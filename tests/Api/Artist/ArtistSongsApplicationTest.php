<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use JsonSerializable;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class ArtistSongsApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $resultItemFactory;

    private ArtistSongsApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = \Mockery::mock(AlbumRepositoryInterface::class);
        $this->resultItemFactory = \Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new ArtistSongsApplication(
            $this->albumRepository,
            $this->resultItemFactory
        );
    }

    public function testRunReturnsSongList(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);
        $disc = \Mockery::mock(DiscInterface::class);
        $song = \Mockery::mock(SongInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);
        $item = \Mockery::mock(JsonSerializable::class);

        $artistId = 666;
        $result = 'some-result';

        $this->albumRepository->shouldReceive('findBy')
            ->with(['artist_id' => $artistId], ['title' => 'ASC'])
            ->once()
            ->andReturn([$album]);

        $album->shouldReceive('getDiscs')
            ->withNoArgs()
            ->once()
            ->andReturn([$disc]);

        $disc->shouldReceive('getSongs')
            ->withNoArgs()
            ->once()
            ->andReturn([$song]);

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($item);

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $item->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

        $stream->shouldReceive('write')
            ->with(json_encode(['items' => [$result]], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['artistId' => (string) $artistId])
        );
    }
}
