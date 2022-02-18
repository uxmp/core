<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistApplicationTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private MockInterface $config;

    private ArtistApplication $subject;

    public function setUp(): void
    {
        $this->artistRepository = \Mockery::mock(ArtistRepositoryInterface::class);
        $this->config = \Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new ArtistApplication(
            $this->artistRepository,
            $this->config
        );
    }

    public function testRunReturnsErrorIfArtistWasNotFound(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $this->artistRepository->shouldReceive('find')
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

    public function testRunReturnsArtist(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $artist = \Mockery::mock(ArtistInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $artistId = 666;
        $title = 'some-title';
        $baseUrl = 'some-base-url';

        $result = [
            'id' => $artistId,
            'name' => $title,
            'cover' => sprintf('%s/art/artist/%d', $baseUrl, $artistId),
        ];

        $this->artistRepository->shouldReceive('find')
            ->with($artistId)
            ->once()
            ->andReturn($artist);

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

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($title);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['artistId' => (string) $artistId])
        );
    }
}
