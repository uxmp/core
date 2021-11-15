<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\Favorite;
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
            'mbid' => $mbid
        ]);
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
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $qb
            ->select('a')
            ->from(Album::class, 'a')
            ->where(
                $expr->in(
                    'a.id',
                    $qbSub
                        ->select('fav.item_id')
                        ->from(Favorite::class, 'fav')
                        ->where($expr->eq('fav.user', '?1'))
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
