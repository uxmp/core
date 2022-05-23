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

class AlbumRecentApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private AlbumRecentApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new AlbumRecentApplication(
            $this->albumRepository,
            $this->config
        );
    }

    public function testRunsReturnsData(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $baseUrl = 'some-base-url';
        $albumId = 666;
        $artistId = 42;
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-album-title';
        $length = 21;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

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
            ->andReturn($albumTitle);
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
            ->andReturn($artistTitle);

        $this->albumRepository->shouldReceive('findBy')
            ->with([], ['last_modified' => 'DESC'], 10)
            ->once()
            ->andReturn([$album]);

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
                json_encode(['items' => [[
                    'id' => $albumId,
                    'artistId' => $artistId,
                    'artistName' => $artistTitle,
                    'name' => $albumTitle,
                    'cover' => sprintf('%s/art/album/%d', $baseUrl, $albumId),
                    'length' => $length,
                ]]], JSON_PRETTY_PRINT),
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
