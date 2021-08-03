<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Container;

final class IntermediateArtist implements IntermediateArtistInterface
{
    private ?string $title = null;

    /** @var array<IntermediateAlbumInterface> */
    private array $albums = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): IntermediateArtistInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getAlbums(): array
    {
        return $this->albums;
    }

    public function addAlbum(IntermediateAlbumInterface $album): IntermediateArtistInterface
    {
        $this->albums[] = $album;
        return $this;
    }
}