<?php

namespace Uxmp\Core\Orm\Model;

use DateTimeInterface;
use Uxmp\Core\Component\Art\CachableArtItemInterface;
use Uxmp\Core\Component\Favorite\FavoriteAbleInterface;

interface AlbumInterface extends
    CachableArtItemInterface,
    FavoriteAbleInterface
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): AlbumInterface;

    public function getArtist(): ArtistInterface;

    public function setArtist(ArtistInterface $artist): AlbumInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): AlbumInterface;

    /**
     * @return iterable<DiscInterface>
     */
    public function getDiscs(): iterable;

    public function getDiscCount(): int;

    public function addDisc(DiscInterface $disc): AlbumInterface;

    public function getCatalog(): CatalogInterface;

    public function setCatalog(CatalogInterface $catalog): AlbumInterface;

    /**
     * Returns the summarized length of all songs on this album
     */
    public function getLength(): int;

    public function getSongCount(): int;

    public function getLastModified(): DateTimeInterface;

    public function setLastModified(DateTimeInterface $last_modified): AlbumInterface;
}
