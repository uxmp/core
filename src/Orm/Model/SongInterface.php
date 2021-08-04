<?php

namespace Usox\Core\Orm\Model;

interface SongInterface
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): SongInterface;

    public function getArtistId(): int;

    public function setArtistId(int $artist_id): SongInterface;

    public function getArtist(): ArtistInterface;

    public function setArtist(ArtistInterface $artist): SongInterface;

    public function getTrackNumber(): int;

    public function setTrackNumber(int $track_number): SongInterface;

    public function getAlbum(): AlbumInterface;

    public function setAlbum(AlbumInterface $album): SongInterface;

    public function getAlbumId(): int;

    public function setAlbumId(int $album_id): SongInterface;

    public function getFilename(): string;

    public function setFilename(string $filename): SongInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): SongInterface;
}