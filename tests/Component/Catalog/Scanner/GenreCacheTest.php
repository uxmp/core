<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMapEnum;
use Uxmp\Core\Orm\Model\GenreMapInterface;
use Uxmp\Core\Orm\Repository\GenreMapRepositoryInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

class GenreCacheTest extends MockeryTestCase
{
    private MockInterface $genreRepository;

    private MockInterface $genreMapRepository;

    private GenreCache $subject;

    public function setUp(): void
    {
        $this->genreRepository = Mockery::mock(GenreRepositoryInterface::class);
        $this->genreMapRepository = Mockery::mock(GenreMapRepositoryInterface::class);

        $this->subject = new GenreCache(
            $this->genreRepository,
            $this->genreMapRepository,
        );
    }

    public function testEnrichDoesNothingIfNoGenresExist(): void
    {
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $album = Mockery::mock(AlbumInterface::class);

        $audioFile->shouldReceive('getGenres')
            ->withNoArgs()
            ->once()
            ->andReturn([]);

        $this->subject->enrich($album, $audioFile);
    }

    public function testEnrichIgnoresExistingGenres(): void
    {
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $genreMap = Mockery::mock(GenreMapInterface::class);

        $genreName = 'some genre';
        $albumId = 666;

        $audioFile->shouldReceive('getGenres')
            ->withNoArgs()
            ->once()
            ->andReturn([$genreName]);

        $this->genreMapRepository->shouldReceive('findBy')
            ->with([
                'mapped_item_type' => GenreMapEnum::ALBUM,
                'mapped_item_id' => $albumId,
            ])
            ->once()
            ->andReturn([$genreMap]);

        $genreMap->shouldReceive('getGenreTitle')
            ->withNoArgs()
            ->once()
            ->andReturn(ucwords($genreName));

        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $this->subject->enrich($album, $audioFile);
    }

    public function testEnrichDeletesExistingGenres(): void
    {
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $genreMap = Mockery::mock(GenreMapInterface::class);

        $genreName1 = 'some genre';
        $genreName2 = 'some other genre';
        $albumId = 666;

        $audioFile->shouldReceive('getGenres')
            ->withNoArgs()
            ->once()
            ->andReturn([$genreName2]);

        $this->genreMapRepository->shouldReceive('findBy')
            ->with([
                'mapped_item_type' => GenreMapEnum::ALBUM,
                'mapped_item_id' => $albumId,
            ])
            ->once()
            ->andReturn([$genreMap, $genreMap]);
        $this->genreMapRepository->shouldReceive('delete')
            ->with($genreMap)
            ->once();

        $genreMap->shouldReceive('getGenreTitle')
            ->withNoArgs()
            ->twice()
            ->andReturn(ucwords($genreName1), ucwords($genreName2));

        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $this->subject->enrich($album, $audioFile);
    }

    public function testEnrichCreates(): void
    {
        $audioFile = Mockery::mock(AudioFileInterface::class);
        $album = Mockery::mock(AlbumInterface::class);
        $genre = Mockery::mock(GenreInterface::class);
        $genreMap = Mockery::mock(GenreMapInterface::class);

        $genreName = 'some genre';
        $albumId = 666;

        $audioFile->shouldReceive('getGenres')
            ->withNoArgs()
            ->once()
            ->andReturn([$genreName]);

        $this->genreRepository->shouldReceive('findOneBy')
            ->with(['title' => ucwords($genreName)])
            ->once()
            ->andReturnNull();
        $this->genreRepository->shouldReceive('prototype->setTitle')
            ->with(ucwords($genreName))
            ->once()
            ->andReturn($genre);
        $this->genreRepository->shouldReceive('save')
            ->with($genre)
            ->once();

        $this->genreMapRepository->shouldReceive('findBy')
            ->with([
                'mapped_item_type' => GenreMapEnum::ALBUM,
                'mapped_item_id' => $albumId,
            ])
            ->once()
            ->andReturn([]);
        $this->genreMapRepository->shouldReceive('prototype->setGenre')
            ->with($genre)
            ->once()
            ->andReturn($genreMap);
        $this->genreMapRepository->shouldReceive('save')
            ->with($genreMap)
            ->once();

        $genreMap->shouldReceive('setMappedItemType')
            ->with(GenreMapEnum::ALBUM)
            ->once()
            ->andReturnSelf();
        $genreMap->shouldReceive('setMappedItemId')
            ->with($albumId)
            ->once()
            ->andReturnSelf();

        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $this->subject->enrich($album, $audioFile);
    }
}
