<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Random;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class RandomSongsApplicationTest extends MockeryTestCase
{
    private MockInterface $songRepository;

    private MockInterface $config;

    private RandomSongsApplication $subject;

    public function setUp(): void
    {
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);
        $this->config = \Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new RandomSongsApplication(
            $this->songRepository,
            $this->config
        );
    }

    public function testRunReturnsList(): void
    {
        $song = \Mockery::mock(SongInterface::class);
        $artist = \Mockery::mock(ArtistInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $request = \Mockery::mock(ServerRequestInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $songId = 666;
        $songTrackNumber = 12345;
        $songTitle = 'some-title';
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-artist-title';
        $albumMbId = 'some-album-mbid';
        $artistId = 33;
        $albumId = 42;
        $baseUrl = 'some-base-url';

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->songRepository->shouldReceive('findAll')
            ->withNoArgs()
            ->once()
            ->andReturn([$song, $song]);

        $song->shouldReceive('getId')
            ->withNoArgs()
            ->twice()
            ->andReturn($songId);
        $song->shouldReceive('getTitle')
            ->withNoArgs()
            ->twice()
            ->andReturn($songTitle);
        $song->shouldReceive('getDisc->getAlbum')
            ->withNoArgs()
            ->twice()
            ->andReturn($album);
        $song->shouldReceive('getTrackNumber')
            ->withNoArgs()
            ->twice()
            ->andReturn($songTrackNumber);

        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->times(2)
            ->andReturn($artist);
        $album->shouldReceive('getId')
            ->withNoArgs()
            ->times(2)
            ->andReturn($albumId);
        $album->shouldReceive('getMbid')
            ->withNoArgs()
            ->times(2)
            ->andReturn($albumMbId);
        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->times(2)
            ->andReturn($albumTitle);

        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->times(2)
            ->andReturn($artistTitle);
        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->times(2)
            ->andReturn($artistId);

        $response->shouldReceive('getBody')
            ->withNoArgs()
            ->once()
            ->andReturn($stream);
        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'application/json')
            ->once()
            ->andReturnSelf();

        $stream->shouldReceive('write')
            ->with(json_encode([
                'items' => [[
                    'id' => $songId,
                    'name' => $songTitle,
                    'artistName' => $artistTitle,
                    'albumName' => $albumTitle,
                    'trackNumber' => $songTrackNumber,
                    'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
                    'cover' => sprintf('%s/art/album/%s', $baseUrl, $albumMbId),
                    'artistId' => $artistId,
                    'albumId' => $albumId,
                ]]
            ], JSON_PRETTY_PRINT))
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['limit' => 1])
        );
    }
}
