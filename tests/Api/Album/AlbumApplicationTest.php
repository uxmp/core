<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private AlbumApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);

        $this->subject = new AlbumApplication(
            $this->albumRepository
        );
    }

    public function testRunReturnsNotFoundIfNotFound(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);

        $this->albumRepository->shouldReceive('find')
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

    public function testRunReturnsAlbumData(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $disc = Mockery::mock(DiscInterface::class);
        $song = Mockery::mock(SongInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $albumId = 666;
        $songId = 42;
        $songTitle = 'some-song-title';
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-album-title';
        $trackNumber = 33;
        $albumMbId = 'some-album-mbid';
        $artistId = 21;
        $discId = 84;

        $this->albumRepository->shouldReceive('find')
            ->with($albumId)
            ->once()
            ->andReturn($album);

        $album->shouldReceive('getDiscs')
            ->withNoArgs()
            ->once()
            ->andReturn([$disc]);
        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);

        $song->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($songId);
        $song->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($songTitle);
        $song->shouldReceive('getTrackNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($trackNumber);

        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumTitle);
        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);
        $album->shouldReceive('getMbid')
            ->withNoArgs()
            ->once()
            ->andReturn($albumMbId);

        $disc->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($discId);
        $disc->shouldReceive('getSongs')
            ->withNoArgs()
            ->once()
            ->andReturn([$song]);

        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistTitle);
        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);

        $result = [
            'id' => $albumId,
            'name' => $albumTitle,
            'artistId' => $artistId,
            'artistName' => $artistTitle,
            'discs' => [[
                'id' => $discId,
                'songs' => [[
                    'id' => $songId,
                    'name' => $songTitle,
                    'artistName' => $artistTitle,
                    'albumName' => $albumTitle,
                    'trackNumber' => $trackNumber,
                    'playUrl' => sprintf('http://localhost:8888/play/%d', $songId),
                    'cover' => sprintf('http://localhost:8888/art/album/%s', $albumMbId),
                    'artistId' => $artistId,
                    'albumId' => $albumId,
                ]]
            ]]
        ];

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);

        $stream->shouldReceive('write')
            ->with(json_encode($result, JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func(
                $this->subject,
                $request,
                $response,
                ['albumId' => (string) $albumId]
            )
        );
    }
}
