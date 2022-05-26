<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\GenreMap;
use Uxmp\Core\Orm\Model\GenreMapInterface;

class GenreMapRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private GenreMapRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new GenreMapRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsGenreMap(): void
    {
        $this->assertInstanceOf(
            GenreMap::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $genreMap = Mockery::mock(GenreMapInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($genreMap)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($genreMap);
    }

    public function testDeleteDeletes(): void
    {
        $genreMap = Mockery::mock(GenreMapInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($genreMap)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($genreMap);
    }

    public function testFindByAlbumReturnsData(): void
    {
        $album = Mockery::mock(AlbumInterface::class);
        $result = Mockery::mock(GenreMapInterface::class);
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $persister = Mockery::mock(EntityPersister::class);

        $albumId = 666;

        $this->entityManager->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->once()
            ->andReturn($unitOfWork);

        $unitOfWork->shouldReceive('getEntityPersister')
            ->with(null)
            ->once()
            ->andReturn($persister);

        $persister->shouldReceive('loadAll')
            ->with(
                [
                    'mapped_item_type' => 'album',
                    'mapped_item_id' => $albumId,
                ],
                null,
                null,
                null,
            )
            ->once()
            ->andReturn([$result]);

        $album->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($albumId);

        $this->assertSame(
            [$result],
            $this->subject->findByAlbum($album)
        );
    }
}
