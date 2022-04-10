<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\PlaylistInterface;

/**
 * @extends ObjectRepository<PlaylistInterface>
 *
 * @method null|PlaylistInterface findOneBy(mixed[] $criteria)
 */
interface PlaylistRepositoryInterface extends ObjectRepository
{
    public function prototype(): PlaylistInterface;

    public function save(PlaylistInterface $playlist): void;

    public function delete(PlaylistInterface $playlist): void;
}
