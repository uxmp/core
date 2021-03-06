<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistListApplicationTest extends MockeryTestCase
{
    private MockInterface $artistRepository;

    private MockInterface $config;

    private ArtistListApplication $subject;

    public function setUp(): void
    {
        $this->artistRepository = Mockery::mock(ArtistRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new ArtistListApplication(
            $this->artistRepository,
            $this->config
        );
    }

    public function testRunReturnsOutput(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $artistId = 666;
        $artistName = 'some-name';
        $baseUrl = 'some-base-url';

        $artist->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($artistId);
        $artist->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistName);

        $this->config->shouldReceive('getBaseUrl')
            ->withNoArgs()
            ->once()
            ->andReturn($baseUrl);

        $this->artistRepository->shouldReceive('findBy')
            ->with([], ['title' => 'ASC'])
            ->once()
            ->andReturn([$artist]);

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
                json_encode(
                    ['items' => [[
                        'id' => $artistId,
                        'name' => $artistName,
                        'cover' => sprintf('%s/art/artist/%d', $baseUrl, $artistId),
                    ]]],
                    JSON_PRETTY_PRINT
                )
            )
            ->once();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }
}
