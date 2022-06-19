<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;

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

    public function testFindFavoritesReturnsData(): void
    {
        $user = Mockery::mock(UserInterface::class);
        $qb = Mockery::mock(QueryBuilder::class);
        $qbSub = Mockery::mock(QueryBuilder::class);
        $expressionBuilder = Mockery::mock(Expr::class);
        $andExpression = Mockery::mock(Expr\Andx::class);
        $inExpression = Mockery::mock(Expr\Func::class);
        $comparisonExpression = Mockery::mock(Expr\Comparison::class);
        $query = Mockery::mock(AbstractQuery::class);

        $inExpressionDql = 'some-in-expression-dql';
        $result = ['some-result'];

        $expressionBuilder->shouldReceive('andX')
            ->withNoArgs()
            ->once()
            ->andReturn($andExpression);
        $expressionBuilder->shouldReceive('eq')
            ->with('fav.user', ':user')
            ->once()
            ->andReturn($comparisonExpression);
        $expressionBuilder->shouldReceive('eq')
            ->with('fav.type', ':type')
            ->once()
            ->andReturn($comparisonExpression);
        $expressionBuilder->shouldReceive('in')
            ->with(
                'a.id',
                $inExpressionDql
            )
            ->once()
            ->andReturn($inExpression);

        $andExpression->shouldReceive('add')
            ->with($comparisonExpression)
            ->twice();

        $this->entityManager->shouldReceive('createQueryBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($qb);
        $this->entityManager->shouldReceive('createQueryBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($qbSub);
        $this->entityManager->shouldReceive('getExpressionBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($expressionBuilder);

        $qbSub->shouldReceive('select')
            ->with('fav.item_id')
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('from')
            ->with(Favorite::class, 'fav')
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('where')
            ->with($andExpression)
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('getDQL')
            ->withNoArgs()
            ->once()
            ->andReturn($inExpressionDql);

        $qb->shouldReceive('select')
            ->with('a')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->with(Song::class, 'a')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->with($inExpression)
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameter')
            ->with('user', $user)
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameter')
            ->with('type', 'song')
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
            $this->subject->findFavorites($user)
        );
    }
}
