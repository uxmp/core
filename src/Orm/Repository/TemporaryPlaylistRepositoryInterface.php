<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\TemporaryPlaylistInterface;

/**
 * @extends ObjectRepository<TemporaryPlaylistInterface>
 *
 * @method null|TemporaryPlaylistInterface findOneBy(mixed[] $criteria)
 */
interface TemporaryPlaylistRepositoryInterface extends ObjectRepository
{
    public function prototype(): TemporaryPlaylistInterface;

    public function save(TemporaryPlaylistInterface $playlist): void;

    public function delete(TemporaryPlaylistInterface $playlist): void;
}
