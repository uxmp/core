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
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private AlbumApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new AlbumApplication(
            $this->albumRepository,
            $this->config,
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
        $artist = Mockery::mock(ArtistInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $albumId = 666;
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-album-title';
        $artistId = 21;
        $baseUrl = 'some-base-url';
        $cover = sprintf('%s/art/album/%d', $baseUrl, $albumId);
        $length = 123;
        $mbId = 'some-mbid';

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->albumRepository->shouldReceive('find')
            ->with($albumId)
            ->once()
            ->andReturn($album);

        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);
        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($albumTitle);
        $album->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);
        $album->shouldReceive('getMbid')
            ->withNoArgs()
            ->once()
            ->andReturn($mbId);

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
            'cover' => $cover,
            'length' => $length,
            'mbId' => $mbId,
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
