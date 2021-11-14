<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\CliInteractorHelper;
use Uxmp\Core\Component\Album\AlbumDeleterInterface;
use Uxmp\Core\Component\Song\SongDeleterInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

class CatalogCleanerTest extends MockeryTestCase
{
    private MockInterface $catalogRepository;

    private MockInterface $albumDeleter;

    private MockInterface $songRepository;

    private MockInterface $songDeleter;

    private MockInterface $discRepository;

    private MockInterface $albumRepository;

    private CatalogCleaner $subject;

    public function setUp(): void
    {
        $this->catalogRepository = \Mockery::mock(CatalogRepositoryInterface::class);
        $this->albumDeleter = \Mockery::mock(AlbumDeleterInterface::class);
        $this->songRepository = \Mockery::mock(SongRepositoryInterface::class);
        $this->songDeleter = \Mockery::mock(SongDeleterInterface::class);
        $this->discRepository = \Mockery::mock(DiscRepositoryInterface::class);
        $this->albumRepository = Mockery::mock(AlbumRepositoryInterface::class);

        $this->subject = new CatalogCleaner(
            $this->catalogRepository,
            $this->albumDeleter,
            $this->songRepository,
            $this->songDeleter,
            $this->discRepository,
            $this->albumRepository,
        );
    }

    public function testCleanFailsWithUnknownCatalog(): void
    {
        $io = \Mockery::mock(CliInteractorHelper::class);

        $catalogId = 666;

        $this->catalogRepository->shouldReceive('find')
            ->with($catalogId)
            ->once()
            ->andReturnNull();

        $io->shouldReceive('error')
            ->with(
                sprintf('Catalog `%d` not found', $catalogId),
                true
            )
            ->once();

        $this->subject->clean($io, $catalogId);
    }

    public function testCleanFailsWithUnreadableCatalogPath(): void
    {
        $io = \Mockery::mock(CliInteractorHelper::class);
        $catalog = \Mockery::mock(CatalogInterface::class);

        $catalogId = 666;
        $directory = '/zomg';

        $this->catalogRepository->shouldReceive('find')
            ->with($catalogId)
            ->once()
            ->andReturn($catalog);

        $catalog->shouldReceive('getPath')
            ->withNoArgs()
            ->once()
            ->andReturn($directory);

        $io->shouldReceive('error')
            ->with(
                sprintf('The path `%s` is not accessible', $directory),
                true
            )
            ->once();

        $this->subject->clean($io, $catalogId);
    }

    public function testCleanDeletes(): void
    {
        $io = \Mockery::mock(CliInteractorHelper::class);
        $catalog = \Mockery::mock(CatalogInterface::class);
        $song = \Mockery::mock(SongInterface::class);
        $disc = \Mockery::mock(DiscInterface::class);
        $album = \Mockery::mock(AlbumInterface::class);

        $catalogId = 666;
        $directory = '/tmp';
        $artistTitle = 'some-artist-title';
        $songTitle = 'some-song-title';
        $discNumber = 42;
        $albumTitle = 'some-album';

        $this->catalogRepository->shouldReceive('find')
            ->with($catalogId)
            ->once()
            ->andReturn($catalog);

        $catalog->shouldReceive('getPath')
            ->withNoArgs()
            ->once()
            ->andReturn($directory);

        $io->shouldReceive('info')
            ->with(
                'Cleaning catalog',
                true
            )
            ->once();
        $io->shouldReceive('info')
            ->with(
                sprintf('Delete `%s - %s`', $artistTitle, $songTitle),
                true
            )
            ->once();
        $io->shouldReceive('info')
            ->with(
                sprintf('Delete disc number `%d` of `%s`', $discNumber, $albumTitle),
                true
            )
            ->once();
        $io->shouldReceive('info')
            ->with(
                sprintf('Delete orphaned album `%s`', $albumTitle),
                true
            )
            ->twice();
        $io->shouldReceive('ok')
            ->with('Done', true)
            ->once();

        $this->songRepository->shouldReceive('findBy')
            ->with(['catalog' => $catalog])
            ->once()
            ->andReturn([$song]);

        $song->shouldReceive('getFilename')
            ->withNoArgs()
            ->once()
            ->andReturn('/zomg');
        $song->shouldReceive('getArtist->getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($artistTitle);
        $song->shouldReceive('getTitle')
            ->withNoArgs()
            ->once()
            ->andReturn($songTitle);

        $this->songDeleter->shouldReceive('delete')
            ->with($song)
            ->once();

        $this->discRepository->shouldReceive('findEmptyDiscs')
            ->with($catalog)
            ->once()
            ->andReturn([$disc]);
        $this->discRepository->shouldReceive('delete')
            ->with($disc)
            ->once();

        $disc->shouldReceive('getAlbum')
            ->withNoArgs()
            ->once()
            ->andReturn($album);
        $disc->shouldReceive('getNumber')
            ->withNoArgs()
            ->once()
            ->andReturn($discNumber);

        $album->shouldReceive('getTitle')
            ->withNoArgs()
            ->times(3)
            ->andReturn($albumTitle);
        $album->shouldReceive('getDiscCount')
            ->withNoArgs()
            ->once()
            ->andReturn(0);

        $this->albumDeleter->shouldReceive('delete')
            ->with($album)
            ->twice();

        $this->albumRepository->shouldReceive('findEmptyAlbums')
            ->with($catalog)
            ->once()
            ->andReturn([$album]);

        $this->subject->clean($io, $catalogId);
    }
}
