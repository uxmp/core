<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use Uxmp\Core\Component\Artist\ArtistCoverUpdaterInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

class AlbumDeleterTest extends MockeryTestCase
{
    private MockInterface $albumRepository;

    private MockInterface $config;

    private MockInterface $artistCoverUpdater;

    private AlbumDeleter $subject;

    public function setUp(): void
    {
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);
        $this->config = Mockery::mock(ConfigProviderInterface::class);
        $this->artistCoverUpdater = Mockery::mock(ArtistCoverUpdaterInterface::class);

        $this->subject = new AlbumDeleter(
            $this->albumRepository,
            $this->config,
            $this->artistCoverUpdater,
        );
    }

    public function testDeleteDeletes(): void
    {
        $album = Mockery::mock(AlbumInterface::class);
        $artist = Mockery::mock(ArtistInterface::class);

        $artItemType = 'some-type';
        $artItemId = '12345';

        $root = vfsStream::setup();

        $assetPath = vfsStream::newDirectory('/img', 0777)
            ->at($root);
        $albumPath = vfsStream::newDirectory('/' . $artItemType, 0777)
            ->at($assetPath);
        vfsStream::newFile(sprintf('%s.jpg', $artItemId))
            ->at($albumPath);

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($root->url());

        $album->shouldReceive('getArtist')
            ->withNoArgs()
            ->once()
            ->andReturn($artist);
        $album->shouldReceive('getArtItemType')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemType);
        $album->shouldReceive('getArtItemId')
            ->withNoArgs()
            ->once()
            ->andReturn($artItemId);

        $this->albumRepository->shouldReceive('delete')
            ->with($album)
            ->once();

        $this->artistCoverUpdater->shouldReceive('update')
            ->with($artist)
            ->once();

        $this->subject->delete($album);
    }
}
