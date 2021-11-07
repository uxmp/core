<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Song;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * @extends EntityRepository<SongInterface>
 *
 * @method null|SongInterface find(int $id)
 */
final class SongRepository extends EntityRepository implements SongRepositoryInterface
{
    #[Pure]
    public function prototype(): SongInterface
    {
        return new Song();
    }

    public function save(SongInterface $song): void
    {
        $this->getEntityManager()->persist($song);
        $this->getEntityManager()->flush();
    }

    public function delete(SongInterface $song): void
    {
        $this->getEntityManager()->remove($song);
        $this->getEntityManager()->flush();
    }

    public function findByMbId(string $mbid): ?SongInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid
        ]);
    }
}
