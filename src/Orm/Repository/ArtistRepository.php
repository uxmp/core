<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\Artist;
use Uxmp\Core\Orm\Model\ArtistInterface;

/**
 * @extends EntityRepository<ArtistInterface>
 */
final class ArtistRepository extends EntityRepository implements ArtistRepositoryInterface
{
    public function prototype(): ArtistInterface
    {
        return new Artist();
    }

    public function save(ArtistInterface $artist): void
    {
        $this->getEntityManager()->persist($artist);
        $this->getEntityManager()->flush();
    }

    public function findByMbId(string $mbid): ?ArtistInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid,
        ]);
    }
}
