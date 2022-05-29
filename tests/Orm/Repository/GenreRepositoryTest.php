<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Uxmp\Core\Orm\Model\Genre;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMap;
use Uxmp\Core\Orm\Model\GenreMapEnum;

class GenreRepositoryTest extends MockeryTestCase
{
    private MockInterface $entityManager;

    private MockInterface $classMetaData;

    private GenreRepository $subject;

    public function setUp(): void
    {
        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->classMetaData = Mockery::mock(ClassMetadata::class);

        $this->subject = new GenreRepository(
            $this->entityManager,
            $this->classMetaData
        );
    }

    public function testPrototypeReturnsGenre(): void
    {
        $this->assertInstanceOf(
            Genre::class,
            $this->subject->prototype()
        );
    }

    public function testSaveSaves(): void
    {
        $genre = Mockery::mock(GenreInterface::class);

        $this->entityManager->shouldReceive('persist')
            ->with($genre)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->save($genre);
    }

    public function testDeleteDeletes(): void
    {
        $genre = Mockery::mock(GenreInterface::class);

        $this->entityManager->shouldReceive('remove')
            ->with($genre)
            ->once();
        $this->entityManager->shouldReceive('flush')
            ->withNoArgs()
            ->once();

        $this->subject->delete($genre);
    }

    public function testGetGenreStatisticsReturnsValue(): void
    {
        $queryBuilder = Mockery::mock(QueryBuilder::class);
        $query = Mockery::mock(AbstractQuery::class);

        $title = 'some-title';
        $albumCount = 666;
        $songCount = 42;

        $this->entityManager->shouldReceive('createQueryBuilder')
            ->withNoArgs()
            ->once()
            ->andReturn($queryBuilder);

        $queryBuilder->shouldReceive('select')
            ->with(
                'a.title',
                'count(b.id) as albumCount',
                'count(c.id) as songCount'
            )
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('from')
            ->with(Genre::class, 'a')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('leftJoin')
            ->with(
                GenreMap::class,
                'b',
                Join::WITH,
                'b.genre_id = a.id AND b.mapped_item_type = :album_index_name'
            )
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('leftJoin')
            ->with(
                GenreMap::class,
                'c',
                Join::WITH,
                'c.genre_id = a.id AND c.mapped_item_type = :song_index_name'
            )
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('setParameters')
            ->with([
                'album_index_name' => GenreMapEnum::ALBUM->value,
                'song_index_name' => GenreMapEnum::SONG->value,
            ])
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('groupBy')
            ->with('a.title')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('orderBy')
            ->with('a.title', 'ASC')
            ->once()
            ->andReturnSelf();
        $queryBuilder->shouldReceive('getQuery')
            ->withNoArgs()
            ->once()
            ->andReturn($query);

        $query->shouldReceive('toIterable')
            ->withNoArgs()
            ->once()
            ->andReturn([[
                'title' => $title,
                'albumCount' => (string) $albumCount,
                'songCount' => (string) $songCount,
            ]]);

        $this->assertSame(
            [[
                'value' => $title,
                'albumCount' => $albumCount,
                'songCount' => $songCount,
            ]],
            iterator_to_array($this->subject->getGenreStatistics())
        );
    }
}
