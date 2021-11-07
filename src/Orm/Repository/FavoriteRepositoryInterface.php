<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\FavoriteInterface;

/**
 * @extends ObjectRepository<FavoriteInterface>
 *
 * @method FavoriteInterface[] findBy(mixed[] $criteria, null|array $order = null, null|int $limit = null, null|int $offset = null)
 */
interface FavoriteRepositoryInterface extends ObjectRepository
{
    public function prototype(): FavoriteInterface;

    public function save(FavoriteInterface $favorite): void;

    public function delete(FavoriteInterface $favorite): void;
}
