<?php

namespace Uxmp\Core\Orm\Model;

use Uxmp\Core\Component\Art\CachableArtItemInterface;

interface ArtistInterface extends
    CachableArtItemInterface
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): ArtistInterface;

    /**
     * @return iterable<AlbumInterface>
     */
    public function getAlbums(): iterable;

    public function addAlbum(AlbumInterface $album): ArtistInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): ArtistInterface;
}
