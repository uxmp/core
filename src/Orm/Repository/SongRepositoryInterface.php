<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\SongInterface;

/**
 * @extends ObjectRepository<SongInterface>
 *
 * @method null|SongInterface find(int $id)
 */
interface SongRepositoryInterface extends ObjectRepository
{
    public function prototype(): SongInterface;

    public function save(SongInterface $song): void;

    public function delete(SongInterface $song): void;

    public function findByMbId(string $mbid): ?SongInterface;
}
