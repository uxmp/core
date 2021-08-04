<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Usox\Core\Orm\Model\Song;
use Usox\Core\Orm\Model\SongInterface;

final class SongRepository extends EntityRepository implements SongRepositoryInterface
{
    public function prototype(): SongInterface
    {
        return new Song();
    }

    public function save(SongInterface $song): void
    {
        $this->getEntityManager()->persist($song);
        $this->getEntityManager()->flush();
    }
}
