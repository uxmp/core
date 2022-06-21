<?php

namespace Uxmp\Core\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Generator;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\UserInterface;

/**
 * @extends ObjectRepository<AlbumInterface>
 */
interface AlbumRepositoryInterface extends ObjectRepository
{
    public function prototype(): AlbumInterface;

    public function save(AlbumInterface $album): void;

    public function findByMbId(string $mbid): ?AlbumInterface;

    /**
     * Returns all albums having a certain genre
     *
     * @return Generator<AlbumInterface>
     */
    public function findByGenre(GenreInterface $genre): Generator;

    public function delete(AlbumInterface $album): void;

    /**
     * @return iterable<AlbumInterface>
     */
    public function getFavorites(UserInterface $user): iterable;

    /**
     * @return iterable<AlbumInterface>
     */
    public function findEmptyAlbums(CatalogInterface $catalog): iterable;
}
