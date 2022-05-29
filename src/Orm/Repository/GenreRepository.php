<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Generator;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Genre;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMap;

/**
 * @extends EntityRepository<GenreInterface>
 *
 * @method null|GenreInterface findOneBy(mixed[] $criteria)
 */
final class GenreRepository extends EntityRepository implements GenreRepositoryInterface
{
    #[Pure]
    public function prototype(): GenreInterface
    {
        return new Genre();
    }

    public function save(GenreInterface $genre): void
    {
        $this->getEntityManager()->persist($genre);
        $this->getEntityManager()->flush();
    }

    public function delete(GenreInterface $genre): void
    {
        $this->getEntityManager()->remove($genre);
        $this->getEntityManager()->flush();
    }

    /**
     * Retrieve a list of all genres and the number of albums/songs associated
     *
     * @return Generator<array{
     *  value: string,
     *  albumCount: int,
     *  songCount: int
     * }>
     */
    public function getGenreStatistics(): Generator
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(
                'a.title',
                'count(b.id) as albumCount',
                'count(c.id) as songCount'
            )
            ->from(Genre::class, 'a')
            ->leftJoin(
                GenreMap::class,
                'b',
                Join::WITH,
                'b.genre_id = a.id AND b.mapped_item_type = \'album\''
            )
            ->leftJoin(
                GenreMap::class,
                'c',
                Join::WITH,
                'c.genre_id = a.id AND c.mapped_item_type = \'song\''
            )
            ->groupBy('a.title')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        foreach ($query->toIterable() as $data) {
            yield [
                'value' => $data['title'],
                'albumCount' => (int) $data['albumCount'],
                'songCount' => (int) $data['songCount'],
            ];
        }
    }
}
