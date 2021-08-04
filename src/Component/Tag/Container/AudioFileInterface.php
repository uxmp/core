<?php

namespace Usox\Core\Component\Tag\Container;

interface AudioFileInterface
{
    public function getTrackNumber(): ?int;

    public function setTrackNumber(?int $track_number): AudioFileInterface;

    public function getFilename(): ?string;

    public function setFilename(?string $filename): AudioFileInterface;

    public function getTitle(): ?string;

    public function setTitle(?string $title): AudioFileInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): AudioFileInterface;

    public function getArtistTitle(): ?string;

    public function setArtistTitle(?string $artist_title): AudioFileInterface;

    public function getArtistMbid(): ?string;

    public function setArtistMbid(?string $artist_mbid): AudioFileInterface;

    public function getAlbumTitle(): ?string;

    public function setAlbumTitle(?string $album_title): AudioFileInterface;

    public function getAlbumMbid(): ?string;

    public function setAlbumMbid(?string $album_mbid): AudioFileInterface;
}
