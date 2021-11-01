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
}
