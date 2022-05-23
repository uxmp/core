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
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\SongInterface;

class SongRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private SongRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new SongRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsInstance(): void
    {
        $this->assertInstanceOf(
            Song::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $song = Mockery::mock(SongInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($song)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($song);
    }

    public function testDeleteDeletes(): void
    {
        $song = Mockery::mock(SongInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($song)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($song);
    }

    public function testFindByMbIdReturnsValue(): void
    {
        $mbid = 'some-mbid';

        $result = Mockery::mock(SongInterface::class);
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $persister = Mockery::mock(EntityPersister::class);

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
}
