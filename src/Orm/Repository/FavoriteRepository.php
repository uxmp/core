<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Uxmp\Core\Orm\Model\Favorite;
use Uxmp\Core\Orm\Model\FavoriteInterface;

/**
 * @extends EntityRepository<FavoriteInterface>
 *
 * @method FavoriteInterface[] findBy(mixed[] $criteria, null|array $order = null, null|int $limit = null, null|int $offset = null)
 */
final class FavoriteRepository extends EntityRepository implements FavoriteRepositoryInterface
{
    public function prototype(): FavoriteInterface
    {
        return new Favorite();
    }

    public function save(FavoriteInterface $favorite): void
    {
        $em = $this->getEntityManager();

        $em->persist($favorite);
        $em->flush();
    }

    public function delete(FavoriteInterface $favorite): void
    {
        $em = $this->getEntityManager();

        $em->remove($favorite);
        $em->flush();
    }
}
