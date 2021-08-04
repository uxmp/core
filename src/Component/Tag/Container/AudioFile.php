<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Container;

final class AudioFile implements AudioFileInterface
{
    private ?string $title = null;

    private ?int $trackNumber = null;

    private ?string $filename = null;

    private ?string $mbid = null;

    private ?string $artistTitle = null;

    private ?string $artistMbid = null;

    private ?string $albumTitle = null;

    private ?string $albumMbid = null;

    public function getTrackNumber(): ?int
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(?int $trackNumber): AudioFileInterface
    {
        $this->trackNumber = $trackNumber;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): AudioFileInterface
    {
        $this->filename = $filename;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): AudioFileInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): AudioFileInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

    public function getArtistTitle(): ?string
    {
        return $this->artistTitle;
    }

    public function setArtistTitle(?string $artistTitle): AudioFileInterface
    {
        $this->artistTitle = $artistTitle;
        return $this;
    }

    public function getArtistMbid(): ?string
    {
        return $this->artistMbid;
    }

    public function setArtistMbid(?string $artistMbid): AudioFileInterface
    {
        $this->artistMbid = $artistMbid;
        return $this;
    }

    public function getAlbumTitle(): ?string
    {
        return $this->albumTitle;
    }

    public function setAlbumTitle(?string $albumTitle): AudioFileInterface
    {
        $this->albumTitle = $albumTitle;
        return $this;
    }

    public function getAlbumMbid(): ?string
    {
        return $this->albumMbid;
    }

    public function setAlbumMbid(?string $albumMbid): AudioFileInterface
    {
        $this->albumMbid = $albumMbid;
        return $this;
    }
}
