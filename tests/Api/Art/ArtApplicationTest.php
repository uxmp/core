<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Art;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Art\ArtItemIdentifierInterface;
use Uxmp\Core\Component\Art\CachableArtItemInterface;
use Uxmp\Core\Component\Art\CachedArtResponseProviderInterface;

class ArtApplicationTest extends MockeryTestCase
{
    private MockInterface $cachedArtResponseProvider;

    private MockInterface $artItemIdentifier;

    private ArtApplication $subject;

    public function setUp(): void
    {
        $this->cachedArtResponseProvider = Mockery::mock(CachedArtResponseProviderInterface::class);
        $this->artItemIdentifier = Mockery::mock(ArtItemIdentifierInterface::class);

        $this->subject = new ArtApplication(
            $this->cachedArtResponseProvider,
            $this->artItemIdentifier,
        );
    }

    public function testRunReturnsNotFoundIfItemIsNull(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);

        $this->artItemIdentifier->shouldReceive('identify')
            ->with('-0')
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

    public function testRunReturnsResponse(): void
    {
        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $item = Mockery::mock(CachableArtItemInterface::class);

        $objectId = 666;
        $objectType = 'some-type';

        $this->artItemIdentifier->shouldReceive('identify')
            ->with(
                sprintf(
                    '%s-%d',
                    $objectType,
                    $objectId
                )
            )
            ->once()
            ->andReturn($item);

        $this->cachedArtResponseProvider->shouldReceive('withCachedArt')
            ->with($response, $item)
            ->once()
            ->andReturn($response);

        $this->assertSame(
            $response,
            call_user_func($this->subject, $request, $response, ['type' => $objectType, 'id' => $objectId])
        );
    }
}
