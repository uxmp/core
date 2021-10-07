<?php

namespace Uxmp\Core\Orm\Model;

use Uxmp\Core\Component\Art\CachableArtItemInterface;

interface AlbumInterface extends
    CachableArtItemInterface
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

    public function getCatalog(): CatalogInterface;

    public function setCatalog(CatalogInterface $catalog): AlbumInterface;

    public function getLength(): int;

    public function getLastModified(): \DateTimeInterface;

    public function setLastModified(\DateTimeInterface $last_modified): AlbumInterface;
}
