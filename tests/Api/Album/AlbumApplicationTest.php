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
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Api\Lib\SongListItemInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private MockInterface $resultItemFactory;

    private AlbumApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->resultItemFactory = Mockery::mock(ResultItemFactoryInterface::class);

        $this->subject = new AlbumApplication(
            $this->albumRepository,
            $this->config,
            $this->resultItemFactory
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
        $songListItem = Mockery::mock(SongListItemInterface::class);

        $albumId = 666;
        $artistTitle = 'some-artist-title';
        $albumTitle = 'some-album-title';
        $albumMbId = 'some-album-mbid';
        $artistId = 21;
        $discId = 84;
        $baseUrl = 'some-base-url';
        $cover = sprintf('%s/art/album/%s', $baseUrl, $albumMbId);
        $length = 123;
        $songResult = 'some-song-result';

        $this->resultItemFactory->shouldReceive('createSongListItem')
            ->with($song, $album)
            ->once()
            ->andReturn($songListItem);

        $songListItem->shouldReceive('jsonSerialize')
            ->withNoArgs()
            ->once()
            ->andReturn($songResult);

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

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
        $disc->shouldReceive('getLength')
            ->withNoArgs()
            ->once()
            ->andReturn($length);

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
                'songs' => [$songResult],
                'length' => $length,
            ]],
            'cover' => $cover,
            'length' => $length,
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
