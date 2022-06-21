<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMap;
use Uxmp\Core\Orm\Model\GenreMapEnum;
use Uxmp\Core\Orm\Model\UserInterface;

class AlbumRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private AlbumRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = \Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = \Mockery::mock(ClassMetadata::class);

        $this->subject = new AlbumRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsAlbum(): void
    {
        $this->assertInstanceOf(
            Album::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $album = \Mockery::mock(AlbumInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($album)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($album);
    }

    public function testFindByMbIdReturnsValue(): void
    {
        $mbid = 'some-mbid';

        $result = \Mockery::mock(AlbumInterface::class);
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

    public function testDeleteDeletes(): void
    {
        $album = \Mockery::mock(AlbumInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($album)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($album);
    }

    public function testFindEmptyAlbumsReturnsResult(): void
    {
        $catalog = Mockery::mock(CatalogInterface::class);
        $query = Mockery::mock(AbstractQuery::class);

        $catalogId = 666;
        $result = [Mockery::mock(AlbumInterface::class)];

        $sql = <<<SQL
            SELECT album
            FROM %s album
            LEFT JOIN %s disc 
            WITH disc.album_id = album.id
            WHERE album.catalog_id = %d
            GROUP BY album HAVING COUNT(disc.id) = 0
            SQL;

        $catalog->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($catalogId);

        $this->entityManager->shouldReceive('createQuery')
            ->with(sprintf(
                $sql,
                Album::class,
                Disc::class,
                $catalogId
            ))
            ->once()
            ->andReturn($query);

        $query->shouldReceive('getResult')
            ->withNoArgs()
            ->once()
            ->andReturn($result);

        $this->subject->findEmptyAlbums($catalog);
    }

    public function testGetFavoritesReturnsData(): void
    {
        $user = Mockery::mock(UserInterface::class);
        $qb = Mockery::mock(QueryBuilder::class);
        $qbSub = Mockery::mock(QueryBuilder::class);
        $expressionBuilder = Mockery::mock(Expr::class);
        $query = Mockery::mock(AbstractQuery::class);

        $result = ['some-result'];
        $qbSubDql = 'some-dql';
        $equalExpression = 'some-eq-expression';
        $inExpression = 'some-in-expression';

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

        $expressionBuilder->shouldReceive('eq')
            ->with('fav.user', '?1')
            ->once()
            ->andReturn($equalExpression);
        $expressionBuilder->shouldReceive('in')
            ->with('a.id', $qbSubDql)
            ->once()
            ->andReturn($inExpression);

        $qbSub->shouldReceive('select')
            ->with('fav.item_id')
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('from')
            ->with(Favorite::class, 'fav')
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('where')
            ->with($equalExpression)
            ->once()
            ->andReturnSelf();
        $qbSub->shouldReceive('getDQL')
            ->withNoArgs()
            ->once()
            ->andReturn($qbSubDql);

        $qb->shouldReceive('select')
            ->with('a')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('from')
            ->with(Album::class, 'a')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('where')
            ->with($inExpression)
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('orderBy')
            ->with('a.title', 'ASC')
            ->once()
            ->andReturnSelf();
        $qb->shouldReceive('setParameter')
            ->with(1, $user)
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
            $this->subject->getFavorites($user)
        );
    }

    public function testFindByGenreReturnsData(): void
    {
        $genre = Mockery::mock(GenreInterface::class);
        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $subQueryBuilder = Mockery::mock(QueryBuilder::class);
        $expressionBuilder = Mockery::mock(Expr::class);
        $andExpression = Mockery::mock(Expr\Andx::class);
        $equalExpression = Mockery::mock(Expr\Comparison::class);
        $inExpression = Mockery::mock(Expr\Func::class);
        $album = Mockery::mock(AlbumInterface::class);
        $query = Mockery::mock(AbstractQuery::class);

        $subQueryBuilderDQL = 'some-dql';

        $this->entityManager->shouldReceive('createQueryBuilder')
            ->withNoArgs()
            ->twice()
            ->andReturn($queryBuilder, $subQueryBuilder);
        $this->entityManager->shouldReceive('getExpressionBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($expressionBuilder);

        $expressionBuilder->shouldReceive('andX')
            ->withNoArgs()
            ->once()
            ->andReturn($andExpression);
        $expressionBuilder->shouldReceive('eq')
            ->with('genre_map.genre', '?1')
            ->once()
            ->andReturn($equalExpression);
        $expressionBuilder->shouldReceive('eq')
            ->with('genre_map.mapped_item_type', '?2')
            ->once()
            ->andReturn($equalExpression);
        $expressionBuilder->shouldReceive('in')
            ->with('a.id', $subQueryBuilderDQL)
            ->once()
            ->andReturn($inExpression);

        $andExpression->shouldReceive('add')
            ->with($equalExpression)
            ->twice();

        $subQueryBuilder->shouldReceive('select')
            ->with('genre_map.mapped_item_id')
            ->once()
            ->andReturnSelf();
        $subQueryBuilder->shouldReceive('from')
            ->with(GenreMap::class, 'genre_map')
            ->once()
            ->andReturnSelf();
        $subQueryBuilder->shouldReceive('where')
            ->with($andExpression)
            ->once()
            ->andReturnSelf();
        $subQueryBuilder->shouldReceive('getDQL')
            ->withNoArgs()
            ->once()
            ->andReturn($subQueryBuilderDQL);

        $queryBuilder->shouldReceive('select')
            ->with('a')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('from')
            ->with(Album::class, 'a')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('where')
            ->with($inExpression)
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('orderBy')
            ->with('a.title', 'ASC')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('setParameter')
            ->with(1, $genre)
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('setParameter')
            ->with(2, GenreMapEnum::ALBUM)
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('getQuery')
            ->withNoArgs()
            ->once()
            ->andReturn($query);

        $query->shouldReceive('toIterable')
            ->withNoArgs()
            ->once()
            ->andReturn([$album]);

        $this->assertSame(
            iterator_to_array($this->subject->findByGenre($genre)),
            [$album],
        );
    }
}
