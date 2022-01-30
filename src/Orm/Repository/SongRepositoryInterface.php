<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends ObjectRepository<SongInterface>
 *
 * @method null|SongInterface find(mixed $id)
 */
interface SongRepositoryInterface extends ObjectRepository
{
    public function prototype(): SongInterface;

    public function save(SongInterface $song): void;

    public function delete(SongInterface $song): void;

    public function findByMbId(string $mbid): ?SongInterface;

    /**
     * Retrieve a complete list of favorite songs for a user
     *
     * @return iterable<SongInterface>
     */
    public function findFavorites(UserInterface $user): iterable;
}
