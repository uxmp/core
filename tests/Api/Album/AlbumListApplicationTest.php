<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumListApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private AlbumListApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new AlbumListApplication(
            $this->albumRepository,
            $this->config
        );
    }

    public function testRunReturnsList(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);

        $albumId = 666;
        $artistId = 42;
        $albumMbId = 'some-mbid';
        $albumName = 'some-album-name';
        $artistName = 'some-artist-name';
        $baseUrl = 'some-base-url';
        $length = 33;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->albumRepository->shouldReceive('findBy')
            ->with([], ['title' => 'ASC'])
            ->once()
            ->andReturn([$album]);

        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);
        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumName);
        $album->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);
        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistName);

        $result = [[
            'id' => $albumId,
            'artistId' => $artistId,
            'artistName' => $artistName,
            'name' => $albumName,
            'cover' => sprintf($baseUrl . '/art/album/%d', $albumId),
            'length' => $length,
        ]];

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
                json_encode(['items' => $result], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    public function testRunReturnsListWithCertainArtist(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(ServerRequestInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $stream = Mockery::mock(StreamInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);

        $albumId = 666;
        $artistId = 42;
        $albumMbId = 'some-mbid';
        $albumName = 'some-album-name';
        $artistName = 'some-artist-name';
        $baseUrl = 'some-base-url';
        $length = 33;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->albumRepository->shouldReceive('findBy')
            ->with(['artist_id' => $artistId], ['title' => 'ASC'])
            ->once()
            ->andReturn([$album]);

        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);
        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);
        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumName);
        $album->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);

        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistName);

        $result = [[
            'id' => $albumId,
            'artistId' => $artistId,
            'artistName' => $artistName,
            'name' => $albumName,
            'cover' => sprintf($baseUrl . '/art/album/%d', $albumId),
            'length' => $length,
        ]];

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
                json_encode(['items' => $result], JSON_PRETTY_PRINT)
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['artistId' => (string) $artistId])
        );
    }
}
