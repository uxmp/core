<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Artist;

use Intervention\Image\Image;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Tzsk\Collage\MakeCollage;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

class ArtistCoverUpdaterTest extends MockeryTestCase
{
    private MockInterface $config;

    private MockInterface $artistRepository;

    private MockInterface $collageMaker;

    private ArtistCoverUpdater $subject;

    private vfsStreamDirectory $root;

    public function setUp(): void
    {
        $this->config = \Mockery::mock(ConfigProviderInterface::class);
        $this->artistRepository = \Mockery::mock(ArtistRepositoryInterface::class);
        $this->collageMaker = \Mockery::mock(MakeCollage::class);
        $this->root = vfsStream::setup();

        $this->subject = new ArtistCoverUpdater(
            $this->config,
            $this->artistRepository,
            $this->collageMaker,
        );
    }

    public function testUpdateDoesNothingIfNoAlbumCoverIsAvailable(): void
    {
        $artist = \Mockery::mock(ArtistInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);

        $albumMbId = 'some-album-mbid';

        $album->shouldReceive('getMbid')
            ->withNoArgs()
            ->once()
            ->andReturn($albumMbId);

        $artist->shouldReceive('getAlbums')
            ->withNoArgs()
            ->once()
            ->andReturn([$album]);

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($this->root->url());

        $this->subject->update($artist);
    }

    public function testUpdateBuildsArtistCover(): void
    {
        $artist = \Mockery::mock(ArtistInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);
        $image = \Mockery::mock(Image::class);

        $albumMbId = 'some-album-mbid';
        $artistMbId = 'some-artist-mbid';

        $assetPath = vfsStream::newDirectory('/img', 0777)
            ->at($this->root);
        $albumPath = vfsStream::newDirectory('/album', 0777)
            ->at($assetPath);
        $artistPath = vfsStream::newDirectory('/artist', 0777)
            ->at($assetPath);

        $imageFile = vfsStream::newFile(sprintf('%s.jpg', $albumMbId))
            ->at($albumPath);

        $album->shouldReceive('getMbid')
            ->withNoArgs()
            ->times(5)
            ->andReturn($albumMbId);

        $artist->shouldReceive('getMbid')
            ->withNoArgs()
            ->once()
            ->andReturn($artistMbId);

        $artist->shouldReceive('getAlbums')
            ->withNoArgs()
            ->once()
            ->andReturn([$album, $album, $album, $album, $album]);

        $this->config->shouldReceive('getAssetPath')
            ->withNoArgs()
            ->once()
            ->andReturn($this->root->url());

        $this->collageMaker->shouldReceive('make')
            ->with(600, 600)
            ->once()
            ->andReturnSelf();
        $this->collageMaker->shouldReceive('padding')
            ->with(10)
            ->once()
            ->andReturnSelf();
        $this->collageMaker->shouldReceive('background')
            ->with('#000')
            ->once()
            ->andReturnSelf();
        $this->collageMaker->shouldReceive('from')
            ->with([$imageFile->url(), $imageFile->url(), $imageFile->url(), $imageFile->url()])
            ->once()
            ->andReturn($image);

        $image->shouldReceive('save')
            ->with(
                sprintf(
                    '%s/%s.jpg',
                    $artistPath->url(),
                    $artistMbId
                )
            )
            ->once();

        $artist->shouldReceive('setLastModified')
            ->with(\Mockery::type(\DateTimeInterface::class))
            ->once();

        $this->artistRepository->shouldReceive('save')
            ->with($artist)
            ->once();

        $this->subject->update($artist);
    }
}
