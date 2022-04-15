<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

class ArtContentRetrieverTest extends MockeryTestCase
{
    private MockInterface $config;

    private ArtContentRetriever $subject;

    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->config = Mockery::mock(ConfigProviderInterface::class);

        $this->subject = new ArtContentRetriever(
            $this->config,
        );

        $this->root = vfsStream::setup();
    }

    public function testRetrieveThrowsExceptionIfArtItemIdIsNull(): void
    {
        $item = Mockery::mock(CachableArtItemInterface::class);

        $this->expectException(Exception\ArtContentException::class);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturnNull();

        $this->subject->retrieve($item);
    }

    public function testRetrieveThrowsExceptionIfFileDoesNotExist(): void
    {
        $item = Mockery::mock(CachableArtItemInterface::class);

        $itemId = 'some-art-id';
        $assetPath = 'some-path';
        $itemType = 'some-art-type';

        $this->expectException(Exception\ArtContentException::class);

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $item->shouldReceive('getArtItemType')
            ->withNoArgs()
            ->once()
            ->andReturn($itemType);

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($assetPath);

        $this->subject->retrieve($item);
    }

    public function testRetrieveReturnsContent(): void
    {
        $item = Mockery::mock(CachableArtItemInterface::class);

        $itemId = 'some-art-id';
        $itemType = 'some-art-type';
        $content = 'some-content';
        $mimeType = 'image/jpg';

        $item->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($itemId);
        $item->shouldReceive('getArtItemType')
            ->withNoArgs()
            ->once()
            ->andReturn($itemType);

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($this->root->url());

        $assetDir = vfsStream::newDirectory('/img')
            ->at($this->root);
        $artTypeDir = vfsStream::newDirectory('/' . $itemType)
            ->at($assetDir);
        vfsStream::newFile($itemId . '.jpg')
            ->withContent($content)
            ->at($artTypeDir);

        $this->assertSame(
            ['mimeType' => $mimeType, 'content' => $content],
            $this->subject->retrieve($item)
        );
    }
}
