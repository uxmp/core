<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

class CachedArtResponseProviderTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $psr17Factory;

    private MockInterface $artContentRetriever;

    private CachedArtResponseProvider $subject;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->psr17Factory = Mockery::mock(Psr17Factory::class);
        $this->artContentRetriever = Mockery::mock(ArtContentRetrieverInterface::class);

        $this->subject = new CachedArtResponseProvider(
            $this->config,
            $this->psr17Factory,
            $this->artContentRetriever,
        );
    }

    public function testWithCachedArtReturnsErrorOnArtRetrievalError(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $item = Mockery::mock(CachableArtItemInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn('some-id');

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($item)
            ->once()
            ->andThrow(new ArtContentException());

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'image/png')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Disposition', 'filename=disc.png')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withBody')
            ->with($stream)
            ->once()
            ->andReturnSelf();

        $this->psr17Factory->shouldReceive('createStreamFromFile')
            ->with(
                realpath(__DIR__ . '/../../../resource/asset/disc.png')
            )
            ->once()
            ->andReturn($stream);

        $this->assertSame(
            $response,
            $this->subject->withCachedArt($response, $item)
        );
    }

    public function testWithCachedArtReturnsErrorIfItemIdIsNull(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $item = Mockery::mock(CachableArtItemInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturnNull();

        $response->shouldReceive('withHeader')
            ->with('Content-Type', 'image/png')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Disposition', 'filename=disc.png')
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withBody')
            ->with($stream)
            ->once()
            ->andReturnSelf();

        $this->psr17Factory->shouldReceive('createStreamFromFile')
            ->with(
                realpath(__DIR__ . '/../../../resource/asset/disc.png')
            )
            ->once()
            ->andReturn($stream);

        $this->assertSame(
            $response,
            $this->subject->withCachedArt($response, $item)
        );
    }

    public function testWithCachedArtReturnsData(): void
    {
        $response = Mockery::mock(ResponseInterface::class);
        $item = Mockery::mock(CachableArtItemInterface::class);
        $stream = Mockery::mock(StreamInterface::class);

        $artItemId = 'some-art-item-id';
        $fileName = sprintf('%s.jpg', $artItemId);
        $timestamp = 123456;
        $maxAge = 666;
        $content = 'some-content';
        $mimeType = 'some-content-type';

        $this->artContentRetriever->shouldReceive('retrieve')
            ->with($item)
            ->once()
            ->andReturn(['content' => $content, 'mimeType' => $mimeType]);

        $lastModified = new DateTime();
        $lastModified->setTimestamp($timestamp);

        $this->config->shouldReceive('getClientCacheMaxAge')
            ->withNoArgs()
            ->once()
            ->andReturn($maxAge);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemId);
        $item->shouldReceive('getLastModified')
            ->withNoArgs()
            ->once()
            ->andReturn($lastModified);

        $response->shouldReceive('withHeader')
            ->with('Last-Modified', $lastModified->format(DATE_RFC7231))
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Cache-Control', sprintf('public, max-age=%d', $maxAge))
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Type', $mimeType)
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withHeader')
            ->with('Content-Disposition', 'filename='.$fileName)
            ->once()
            ->andReturnSelf();
        $response->shouldReceive('withBody')
            ->with($stream)
            ->once()
            ->andReturnSelf();

        $this->psr17Factory->shouldReceive('createStream')
            ->with($content)
            ->once()
            ->andReturn($stream);

        $this->assertSame(
            $response,
            $this->subject->withCachedArt($response, $item)
        );
    }
}
