<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * @extends EntityRepository<SongInterface>
 */
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

    public function findByMbId(string $mbid): ?SongInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid
        ]);
    }
}
