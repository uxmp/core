<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Art;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Art\CachableArtItemInterface;
use Uxmp\Core\Component\Art\CachedArtResponseProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtApplicationTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $cachedArtResponseProvider;

    private MockInterface $artistRepository;

    private ArtApplication $subject;

    public function setUp(): void
    {
        $this->albumRepository = \Mockery::mock(AlbumRepositoryInterface::class);
        $this->cachedArtResponseProvider = \Mockery::mock(CachedArtResponseProviderInterface::class);
        $this->artistRepository = \Mockery::mock(ArtistRepositoryInterface::class);

        $this->subject = new ArtApplication(
            $this->albumRepository,
            $this->cachedArtResponseProvider,
            $this->artistRepository,
        );
    }

    public function testRunReturnsNotFoundIfItemIsNull(): void
    {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $response->shouldReceive('withStatus')
            ->with(StatusCode::NOT_FOUND)
            ->once()
            ->andReturnSelf();

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, [])
        );
    }

    /**
     * @dataProvider artDataProvider
     */
    public function testRunReturnsResponse(
        string $type,
        string $repository
    ): void {
        $request = \Mockery::mock(ServerRequestInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $item = \Mockery::mock(CachableArtItemInterface::class);

        $objectId = 666;

        $this->{$repository}->shouldReceive('find')
            ->with($objectId)
            ->once()
            ->andReturn($item);

        $this->cachedArtResponseProvider->shouldReceive('withCachedArt')
            ->with($response, $item)
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['type' => $type, 'id' => $objectId])
        );
    }

    public function artDataProvider(): array
    {
        return [
            ['album', 'albumRepository'],
            ['artist', 'artistRepository'],
        ];
    }
}
