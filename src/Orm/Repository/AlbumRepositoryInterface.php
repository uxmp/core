<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends ObjectRepository<AlbumInterface>
 */
interface AlbumRepositoryInterface extends ObjectRepository
{
    public function prototype(): AlbumInterface;

    public function save(AlbumInterface $album): void;

    public function findByMbId(string $mbid): ?AlbumInterface;

    public function delete(AlbumInterface $album): void;

    /**
     * @return iterable<AlbumInterface>
     */
    public function getFavorites(UserInterface $user): iterable;
}
