<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\Disc;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\Song;

/**
 * @extends EntityRepository<DiscInterface>
 */
final class DiscRepository extends EntityRepository implements DiscRepositoryInterface
{
    public function prototype(): DiscInterface
    {
        return new Disc();
    }

    public function save(DiscInterface $disc): void
    {
        $this->getEntityManager()->persist($disc);
        $this->getEntityManager()->flush();
    }

    public function delete(DiscInterface $disc): void
    {
        $this->getEntityManager()->remove($disc);
        $this->getEntityManager()->flush();
    }

    /**
     * Find a unique disc by its mbid and the disc number within a release group
     */
    public function findUniqueDisc(
        string $musicBrainzDiscId,
        int $discNumber
    ): ?DiscInterface {
        return $this->findOneBy([
            'mbid' => $musicBrainzDiscId,
            'number' => $discNumber,
        ]);
    }

    public function findEmptyDiscs(CatalogInterface $catalog): array
    {
        $query = <<<SQL
            SELECT disc
            FROM %s disc
            LEFT JOIN %s song 
            WITH song.disc_id = disc.id
            LEFT JOIN %s album
            WITH album.id = disc.album_id
            WHERE album.catalog_id = %d
            GROUP BY disc HAVING COUNT(song.id) = 0
            SQL;

        return $this->getEntityManager()
            ->createQuery(sprintf(
                $query,
                Disc::class,
                Song::class,
                Album::class,
                $catalog->getId(),
            ))
            ->getResult();
    }
}
