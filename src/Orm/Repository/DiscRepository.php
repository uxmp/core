<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
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

    public function findByMbId(string $mbid): ?DiscInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid
        ]);
    }

    public function findEmptyDiscs(CatalogInterface $catalog): array
    {
        $query = <<<SQL
        SELECT disc
        FROM %s disc
        LEFT JOIN %s song 
        WITH song.disc_id = disc.id
        WHERE song.catalog_id = %d
        GROUP BY disc HAVING COUNT(song.id) = 0
        SQL;

        return $this->getEntityManager()
            ->createQuery(sprintf(
                $query,
                Disc::class,
                Song::class,
                $catalog->getId(),
            ))
            ->getResult();
    }
}
