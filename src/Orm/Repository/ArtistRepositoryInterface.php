<?php

namespace Usox\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Usox\Core\Orm\Model\ArtistInterface;

interface ArtistRepositoryInterface extends ObjectRepository
{
    public function prototype(): ArtistInterface;

    public function save(ArtistInterface $artist): void;
}