<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\PlaybackHistory;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;
use Uxmp\Core\Orm\Model\SongInterface;

class PlaybackHistoryRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private PlaybackHistoryRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new PlaybackHistoryRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsInstance(): void
    {
        $this->assertInstanceOf(
            PlaybackHistory::class,
            $this->subject->prototype()
        );
    }

    public function testGetMostPlayedReturnsData(): void
    {
        $qb = Mockery::mock(QueryBuilder::class);
        $query = Mockery::mock(AbstractQuery::class);

        $result = ['some-result'];

        $this->entityManager->shouldReceive('createQueryBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($qb);

        $qb->shouldReceive('select')
            ->with('COUNT(c) as cnt, c.song_id')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->with(PlaybackHistory::class, 'c')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('groupBy')
            ->with('c.song_id')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('orderBy')
            ->with('cnt', 'DESC')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setMaxResults')
            ->with(10)
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('getQuery')
            ->withNoArgs()
            ->once()
            ->andReturn($query);

        $query->shouldReceive('getResult')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

        $this->assertSame(
            $result,
            $this->subject->getMostPlayed()
        );
    }

    public function testSaveSaves(): void
    {
        $item = Mockery::mock(PlaybackHistoryInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($item)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($item);
    }

    public function testDeleteDeletes(): void
    {
        $item = Mockery::mock(PlaybackHistoryInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($item)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($item);
    }

    public function testFindBySongReturnsValue(): void
    {
        $result = Mockery::mock(PlaybackHistoryInterface::class);
        $song = Mockery::mock(SongInterface::class);
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

        $persister->shouldReceive('loadAll')
            ->with(['song' => $song], null, null, null)
            ->once()
            ->andReturn([$result]);

        $this->assertSame(
            [$result],
            $this->subject->findBySong($song)
        );
    }
}
