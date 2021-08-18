<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\AlbumInterface;

/**
 * @extends ObjectRepository<AlbumInterface>
 */
interface AlbumRepositoryInterface extends ObjectRepository
{
    public function prototype(): AlbumInterface;

    public function save(AlbumInterface $album): void;

    public function findByMbId(string $mbid): ?AlbumInterface;
}
