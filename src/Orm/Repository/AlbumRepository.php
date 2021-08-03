<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use Usox\Core\Orm\Model\Album;
use Usox\Core\Orm\Model\AlbumInterface;

final class AlbumRepository extends EntityRepository implements AlbumRepositoryInterface
{
    public function prototype(): AlbumInterface
    {
        return new Album();
    }

    public function save(AlbumInterface $album): void
    {
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }
}