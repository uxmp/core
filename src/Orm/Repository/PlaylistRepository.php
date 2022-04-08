<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Playlist;
use Uxmp\Core\Orm\Model\PlaylistInterface;

/**
 * @extends EntityRepository<PlaylistInterface>
 *
 * @method null|PlaylistInterface findOneBy(mixed[] $criteria)
 * @method null|PlaylistInterface find(mixed $id)
 */
final class PlaylistRepository extends EntityRepository implements PlaylistRepositoryInterface
{
    #[Pure]
    public function prototype(): PlaylistInterface
    {
        return new Playlist();
    }

    public function save(PlaylistInterface $playlist): void
    {
        $this->getEntityManager()->persist($playlist);
        $this->getEntityManager()->flush();
    }

    public function delete(PlaylistInterface $playlist): void
    {
        $this->getEntityManager()->remove($playlist);
        $this->getEntityManager()->flush();
    }
}
