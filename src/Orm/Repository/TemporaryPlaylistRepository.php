<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\TemporaryPlaylist;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;

/**
 * @extends EntityRepository<TemporaryPlaylistInterface>
 *
 * @method null|PlaylistInterface findOneBy(mixed[] $criteria)
 * @method null|PlaylistInterface find(mixed $id)
 */
final class TemporaryPlaylistRepository extends EntityRepository implements TemporaryPlaylistRepositoryInterface
{
    #[Pure]
    public function prototype(): TemporaryPlaylistInterface
    {
        return new TemporaryPlaylist();
    }

    public function save(TemporaryPlaylistInterface $playlist): void
    {
        $this->getEntityManager()->persist($playlist);
        $this->getEntityManager()->flush();
    }

    public function delete(TemporaryPlaylistInterface $playlist): void
    {
        $this->getEntityManager()->remove($playlist);
        $this->getEntityManager()->flush();
    }
}
