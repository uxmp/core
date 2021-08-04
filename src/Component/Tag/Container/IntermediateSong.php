<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Container;

final class IntermediateSong implements IntermediateSongInterface
{
    private ?string $title = null;

    private ?int $track_number = null;

    private ?string $filename = null;

    private ?string $mbid = null;

    public function getTrackNumber(): ?int
    {
        return $this->track_number;
    }

    public function setTrackNumber(?int $track_number): IntermediateSongInterface
    {
        $this->track_number = $track_number;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): IntermediateSongInterface
    {
        $this->filename = $filename;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): IntermediateSongInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): IntermediateSongInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

}