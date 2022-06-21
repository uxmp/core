<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Generator;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMap;
use Uxmp\Core\Orm\Model\GenreMapEnum;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends EntityRepository<AlbumInterface>
 *
 * @method AlbumInterface[] findBy(mixed[] $criteria, null|array $order = null, null|int $limit = null, null|int $offset = null)
 */
final class AlbumRepository extends EntityRepository implements AlbumRepositoryInterface
{
    public function prototype(): AlbumInterface
    {
        return new Album();
    }

    public function save(AlbumInterface $album): void
    {
        $em = $this->getEntityManager();

        $em->persist($album);
        $em->flush();
    }

    public function findByMbId(string $mbid): ?AlbumInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid,
        ]);
    }

    /**
     * Returns all albums having a certain genre
     *
     * @return Generator<AlbumInterface>
     */
    public function findByGenre(GenreInterface $genre): Generator
    {
        $queryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $subQueryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $expressionBuilder = $this
            ->getEntityManager()
            ->getExpressionBuilder();

        $andExpression = $expressionBuilder->andX();
        $andExpression->add($expressionBuilder->eq('genre_map.genre', '?1'));
        $andExpression->add($expressionBuilder->eq('genre_map.mapped_item_type', '?2'));

        $queryBuilder
            ->select('a')
            ->from(Album::class, 'a')
            ->where(
                $expressionBuilder->in(
                    'a.id',
                    $subQueryBuilder
                        ->select('genre_map.mapped_item_id')
                        ->from(GenreMap::class, 'genre_map')
                        ->where($andExpression)
                        ->getDQL()
                )
            )
            ->orderBy('a.title', 'ASC')
            ->setParameter(1, $genre)
            ->setParameter(2, GenreMapEnum::ALBUM)
        ;

        foreach ($queryBuilder->getQuery()->toIterable() as $item) {
            yield $item;
        }
    }

    public function delete(AlbumInterface $album): void
    {
        $em = $this->getEntityManager();

        $em->remove($album);
        $em->flush();
    }

    public function getFavorites(UserInterface $user): iterable
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $qbSub = $this
            ->getEntityManager()
            ->createQueryBuilder();
        $expressionBuilder = $this
            ->getEntityManager()
            ->getExpressionBuilder();

        $qb
            ->select('a')
            ->from(Album::class, 'a')
            ->where(
                $expressionBuilder->in(
                    'a.id',
                    $qbSub
                        ->select('fav.item_id')
                        ->from(Favorite::class, 'fav')
                        ->where($expressionBuilder->eq('fav.user', '?1'))
                        ->getDQL()
                )
            )
            ->orderBy('a.title', 'ASC')
            ->setParameter(1, $user);

        return $qb->getQuery()->getResult();
    }

    public function findEmptyAlbums(CatalogInterface $catalog): iterable
    {
        $query = <<<SQL
            SELECT album
            FROM %s album
            LEFT JOIN %s disc 
            WITH disc.album_id = album.id
            WHERE album.catalog_id = %d
            GROUP BY album HAVING COUNT(disc.id) = 0
            SQL;

        return $this->getEntityManager()
            ->createQuery(sprintf(
                $query,
                Album::class,
                Disc::class,
                $catalog->getId(),
            ))
            ->getResult();
    }
}
