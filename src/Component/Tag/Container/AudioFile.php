<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Container;

final class AudioFile implements AudioFileInterface
{
    private string $title = '';

    private int $trackNumber = 0;

    private string $filename = '';

    private string $mbid = '';

    private string $artistTitle = '';

    private string $artistMbid = '';

    private string $albumTitle = '';

    private string $albumMbid = '';

    private string $discMbid = '';

    private int $discNumber = 0;

    private ?int $year = null;

    private int $fileSize = 0;

    private string $mimeType = '';

    /** @var array<string> */
    private array $genres = [];

    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(int $trackNumber): AudioFileInterface
    {
        $this->trackNumber = $trackNumber;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): AudioFileInterface
    {
        $this->filename = $filename;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): AudioFileInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getMbid(): string
    {
        return $this->mbid;
    }

    public function setMbid(string $mbid): AudioFileInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

    public function getArtistTitle(): string
    {
        return $this->artistTitle;
    }

    public function setArtistTitle(string $artistTitle): AudioFileInterface
    {
        $this->artistTitle = $artistTitle;
        return $this;
    }

    public function getArtistMbid(): string
    {
        return $this->artistMbid;
    }

    public function setArtistMbid(string $artistMbid): AudioFileInterface
    {
        $this->artistMbid = $artistMbid;
        return $this;
    }

    public function getAlbumTitle(): string
    {
        return $this->albumTitle;
    }

    public function setAlbumTitle(string $albumTitle): AudioFileInterface
    {
        $this->albumTitle = $albumTitle;
        return $this;
    }

    public function getAlbumMbid(): string
    {
        return $this->albumMbid;
    }

    public function setAlbumMbid(string $albumMbid): AudioFileInterface
    {
        $this->albumMbid = $albumMbid;
        return $this;
    }

    public function getDiscMbid(): string
    {
        return $this->discMbid;
    }

    public function setDiscMbid(string $discMbid): AudioFileInterface
    {
        $this->discMbid = $discMbid;
        return $this;
    }

    public function getDiscNumber(): int
    {
        return $this->discNumber;
    }

    public function setDiscNumber(int $discNumber): AudioFileInterface
    {
        $this->discNumber = $discNumber;
        return $this;
    }

    public function isValid(): bool
    {
        return
            $this->mbid !== '' &&
            $this->artistMbid !== '' &&
            $this->albumMbid !== '' &&
            $this->mimeType !== '';
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): AudioFileInterface
    {
        $this->year = $year;
        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): AudioFileInterface
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @param array<string> $genres
     */
    public function setGenres(array $genres): AudioFileInterface
    {
        $this->genres = $genres;
        return $this;
    }

    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): AudioFileInterface
    {
        $this->fileSize = $fileSize;
        return $this;
    }
}
