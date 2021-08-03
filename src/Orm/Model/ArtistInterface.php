<?php

namespace Usox\Core\Orm\Model;

use Doctrine\ORM\PersistentCollection;

interface ArtistInterface
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): ArtistInterface;

    /**
     * @return iterable<AlbumInterface>
     */
    public function getAlbums(): iterable;
}