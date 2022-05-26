<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\GenreMapInterface;

/**
 * @extends ObjectRepository<GenreMapInterface>
 *
 * @method null|GenreMapInterface findOneBy(mixed[] $criteria)
 */
interface GenreMapRepositoryInterface extends ObjectRepository
{
    public function prototype(): GenreMapInterface;

    public function save(GenreMapInterface $genreMap): void;

    public function delete(GenreMapInterface $genreMap): void;

    /**
     * @return iterable<GenreMapInterface>
     */
    public function findByAlbum(AlbumInterface $album): iterable;
}
