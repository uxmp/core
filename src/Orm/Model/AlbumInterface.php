<?php

namespace Usox\Core\Orm\Model;

interface AlbumInterface
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): AlbumInterface;

    public function getArtistId(): int;

    public function setArtistId(int $artist_id): AlbumInterface;

    public function getArtist(): ArtistInterface;

    public function setArtist(ArtistInterface $artist): AlbumInterface;

    /**
     * @return iterable<SongInterface>
     */
    public function getSongs(): iterable;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): AlbumInterface;
}
