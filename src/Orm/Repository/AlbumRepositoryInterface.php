<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\AlbumInterface;

/**
 * @extends ObjectRepository<AlbumInterface>
 */
interface AlbumRepositoryInterface extends ObjectRepository
{
    public function prototype(): AlbumInterface;

    public function save(AlbumInterface $album): void;
}
