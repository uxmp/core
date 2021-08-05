<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\SongInterface;

/**
 * @extends ObjectRepository<SongInterface>
 */
interface SongRepositoryInterface extends ObjectRepository
{
    public function prototype(): SongInterface;

    public function save(SongInterface $song): void;

    public function findByMbId(string $mbid): ?SongInterface;
}
