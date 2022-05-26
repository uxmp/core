<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\Genre;
use Uxmp\Core\Orm\Model\GenreInterface;

/**
 * @extends EntityRepository<GenreInterface>
 *
 * @method null|GenreInterface findOneBy(mixed[] $criteria)
 */
final class GenreRepository extends EntityRepository implements GenreRepositoryInterface
{
    #[Pure]
    public function prototype(): GenreInterface
    {
        return new Genre();
    }

    public function save(GenreInterface $genre): void
    {
        $this->getEntityManager()->persist($genre);
        $this->getEntityManager()->flush();
    }

    public function delete(GenreInterface $genre): void
    {
        $this->getEntityManager()->remove($genre);
        $this->getEntityManager()->flush();
    }
}
