<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Container;

final class IntermediateAlbum implements IntermediateAlbumInterface
{
    private ?string $artist = null;

    private ?string $title = null;

    /** @var array<IntermediateSongInterface> */
    private array $songs = [];

    private ?string $mbid = null;

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): IntermediateAlbumInterface
    {
        $this->artist = $artist;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): IntermediateAlbumInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getSongs(): array
    {
        return $this->songs;
    }

    public function addSong(IntermediateSongInterface $song): IntermediateAlbumInterface
    {
        $this->songs[] = $song;

        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): IntermediateAlbumInterface
    {
        $this->mbid = $mbid;
        return $this;
    }
}
