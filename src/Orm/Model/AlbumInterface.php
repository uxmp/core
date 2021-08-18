<?php

namespace Uxmp\Core\Orm\Model;

interface AlbumInterface
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
}
