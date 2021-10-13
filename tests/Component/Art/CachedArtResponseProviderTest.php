<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

class CachedArtResponseProviderTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $psr17Factory;

    private CachedArtResponseProvider $subject;

    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->config = \Mockery::mock(ConfigProviderInterface::class);
        $this->psr17Factory = \Mockery::mock(Psr17Factory::class);
        $this->root = vfsStream::setup();

        $this->subject = new CachedArtResponseProvider(
            $this->config,
            $this->psr17Factory
        );
    }

    public function testWithCachedArtReturnsErrorIfItemIdIsNull(): void
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $item = \Mockery::mock(CachableArtItemInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

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

    public function testWithCachedArtReturnsErrorIfArtFileDoesNotExist(): void
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $item = \Mockery::mock(CachableArtItemInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $artItemId = 'some-art-item-id';
        $assetPath = 'some-asset-path';
        $artItemType = 'some-art-item-type';

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemId);
        $item->shouldReceive('getArtItemType')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemType);

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

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($assetPath);

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
        $response = \Mockery::mock(ResponseInterface::class);
        $item = \Mockery::mock(CachableArtItemInterface::class);
        $stream = \Mockery::mock(StreamInterface::class);

        $artItemId = 'some-art-item-id';
        $artItemType = 'some-art-item-type';
        $fileName = sprintf('%s.jpg', $artItemId);
        $timestamp = 123456;
        $maxAge = 666;

        $lastModified = new \DateTime();
        $lastModified->setTimestamp($timestamp);

        $assetDir = vfsStream::newDirectory('/img')
            ->at($this->root);
        $artTypeDir = vfsStream::newDirectory('/' . $artItemType)
            ->at($assetDir);
        vfsStream::newFile($fileName)
            ->withContent('aggi')
            ->at($artTypeDir);

        $this->config->shouldReceive('getClientCacheMaxAge')
            ->withNoArgs()
            ->once()
            ->andReturn($maxAge);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemId);
        $item->shouldReceive('getArtItemType')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemType);
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
            ->with('Content-Type', 'image/jpg')
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

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($this->root->url());

        $this->psr17Factory->shouldReceive('createStreamFromFile')
            ->with(
                sprintf(
                    '%s/%s',
                    $artTypeDir->url(),
                    $fileName,
                )
            )
            ->once()
            ->andReturn($stream);

        $this->assertSame(
            $response,
            $this->subject->withCachedArt($response, $item)
        );
    }
}
