<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\ORM\EntityRepository;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\GenreMap;
use Uxmp\Core\Orm\Model\GenreMapInterface;

/**
 * @extends EntityRepository<GenreMapInterface>
 *
 * @method null|GenreMapInterface findOneBy(mixed[] $criteria)
 */
final class GenreMapRepository extends EntityRepository implements GenreMapRepositoryInterface
{
    #[Pure]
    public function prototype(): GenreMapInterface
    {
        return new GenreMap();
    }

    public function save(GenreMapInterface $genreMap): void
    {
        $this->getEntityManager()->persist($genreMap);
        $this->getEntityManager()->flush();
    }

    public function delete(GenreMapInterface $genreMap): void
    {
        $this->getEntityManager()->remove($genreMap);
        $this->getEntityManager()->flush();
    }

    /**
     * @return iterable<GenreMapInterface>
     */
    public function findByAlbum(AlbumInterface $album): iterable
    {
        return $this->findBy([
            'mapped_item_type' => 'album',
            'mapped_item_id' => $album->getId(),
        ]);
    }
}
