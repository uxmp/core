<?php

namespace Usox\Core\Component\Tag\Container;

interface IntermediateArtistInterface
{
    public function getTitle(): ?string;

    public function setTitle(?string $title): IntermediateArtistInterface;

    /**
     * @return array<IntermediateAlbumInterface>
     */
    public function getAlbums(): array;

    public function addAlbum(IntermediateAlbumInterface $album): IntermediateArtistInterface;
}