<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\Song;

class DiscRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private DiscRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = \Mockery::mock(ClassMetadata::class);

        $this->subject = new DiscRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsDiscInstance(): void
    {
        $this->assertInstanceOf(
            Disc::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $disc = \Mockery::mock(DiscInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($disc)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($disc);
    }

    public function testDeleteDeletes(): void
    {
        $disc = \Mockery::mock(DiscInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($disc)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($disc);
    }

    public function testFindByMbIdReturnsValue(): void
    {
        $mbid = 'some-mbid';

        $result = \Mockery::mock(DiscInterface::class);
        $unitOfWork = \Mockery::mock(UnitOfWork::class);
        $persister = \Mockery::mock(EntityPersister::class);

        $this->entityManager->shouldReceive('getUnitOfWork')
            ->withNoArgs()
            ->once()
            ->andReturn($unitOfWork);

        $unitOfWork->shouldReceive('getEntityPersister')
            ->with(null)
            ->once()
            ->andReturn($persister);

        $persister->shouldReceive('load')
            ->with(['mbid' => $mbid], null, null, [], null, 1, null)
            ->once()
            ->andReturn($result);

        $this->assertSame(
            $result,
            $this->subject->findByMbId($mbid)
        );
    }

    public function testFindEmptyDiscsReturnsData(): void
    {
        $query = \Mockery::mock(AbstractQuery::class);
        $result = [\Mockery::mock(DiscInterface::class)];
        $catalog = \Mockery::mock(CatalogInterface::class);

        $catalogId = 666;

        $catalog->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($catalogId);

        $sql = <<<SQL
        SELECT disc
        FROM %s disc
        LEFT JOIN %s song 
        WITH song.disc_id = disc.id
        LEFT JOIN %s album
        WITH album.id = disc.album_id
        WHERE album.catalog_id = %d
        GROUP BY disc HAVING COUNT(song.id) = 0
        SQL;

        $this->entityManager->shouldReceive('createQuery')
            ->with(sprintf(
                $sql,
                Disc::class,
                Song::class,
                Album::class,
                $catalogId
            ))
            ->once()
            ->andReturn($query);

        $query->shouldReceive('getResult')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

        $this->assertSame(
            $result,
            $this->subject->findEmptyDiscs($catalog)
        );
    }
}
