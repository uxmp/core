<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\SongInterface;

interface SongRepositoryInterface extends ObjectRepository
{
    public function prototype(): SongInterface;

    public function save(SongInterface $song): void;
}
