<?php

namespace Uxmp\Core\Component\Tag\Container;

interface AudioFileInterface
{
    public function getTrackNumber(): int;

    public function setTrackNumber(int $trackNumber): AudioFileInterface;

    public function getFilename(): string;

    public function setFilename(string $filename): AudioFileInterface;

    public function getTitle(): string;

    public function setTitle(string $title): AudioFileInterface;

    public function getMbid(): string;

    public function setMbid(string $mbid): AudioFileInterface;

    public function getArtistTitle(): string;

    public function setArtistTitle(string $artistTitle): AudioFileInterface;

    public function getArtistMbid(): string;

    public function setArtistMbid(string $artistMbid): AudioFileInterface;

    public function getAlbumTitle(): string;

    public function setAlbumTitle(string $albumTitle): AudioFileInterface;

    public function getAlbumMbid(): string;

    public function setAlbumMbid(string $albumMbid): AudioFileInterface;

    public function getDiscMbid(): string;

    public function setDiscMbid(string $discMbid): AudioFileInterface;

    public function getDiscNumber(): int;

    public function setDiscNumber(int $discNumber): AudioFileInterface;

    public function isValid(): bool;

    public function getYear(): ?int;

    public function setYear(?int $year): AudioFileInterface;

    public function getMimeType(): string;

    public function setMimeType(string $mimeType): AudioFileInterface;

    /**
     * @return array<string>
     */
    public function getGenres(): array;

    /**
     * @param array<string> $genres
     */
    public function setGenres(array $genres): AudioFileInterface;

    public function getFileSize(): int;

    public function setFileSize(int $fileSize): AudioFileInterface;
}
