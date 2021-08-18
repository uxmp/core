<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\Album;
use Uxmp\Core\Orm\Model\AlbumInterface;

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
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }

    public function findByMbId(string $mbid): ?AlbumInterface
    {
        return $this->findOneBy([
            'mbid' => $mbid
        ]);
    }
}
