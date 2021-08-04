<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Usox\Core\Orm\Model\Artist;
use Usox\Core\Orm\Model\ArtistInterface;

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
}
