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
use Uxmp\Core\Orm\Model\GenreMapEnum;

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
     *  id: int,
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
                'a.id',
                'a.title',
                'count(b.id) as albumCount',
                'count(c.id) as songCount'
            )
            ->from(Genre::class, 'a')
            ->leftJoin(
                GenreMap::class,
                'b',
                Join::WITH,
                'b.genre_id = a.id AND b.mapped_item_type = :album_index_name'
            )
            ->leftJoin(
                GenreMap::class,
                'c',
                Join::WITH,
                'c.genre_id = a.id AND c.mapped_item_type = :song_index_name'
            )
            ->setParameters([
                'album_index_name' => GenreMapEnum::ALBUM->value,
                'song_index_name' => GenreMapEnum::SONG->value,
            ])
            ->groupBy('a.title')
            ->orderBy('a.title', 'ASC')
            ->getQuery();

        foreach ($query->toIterable() as $data) {
            yield [
                'id' => (int) $data['id'],
                'value' => $data['title'],
                'albumCount' => (int) $data['albumCount'],
                'songCount' => (int) $data['songCount'],
            ];
        }
    }
}
