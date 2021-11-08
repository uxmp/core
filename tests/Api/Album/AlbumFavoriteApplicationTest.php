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
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumFavoriteApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private AlbumFavoriteApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new AlbumFavoriteApplication(
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
        $user = Mockery::mock(UserInterface::class);

        $albumId = 666;
        $artistId = 42;
        $albumName = 'some-album-name';
        $artistName = 'some-artist-name';
        $baseUrl = 'some-base-url';
        $length = 33;

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->albumRepository->shouldReceive('getFavorites')
            ->with($user)
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

        $request->shouldReceive('getAttribute')
            ->with(SessionValidatorMiddleware::USER)
            ->once()
            ->andReturn($user);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
