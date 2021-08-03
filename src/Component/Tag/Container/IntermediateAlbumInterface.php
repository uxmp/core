<?php

namespace Usox\Core\Component\Tag\Container;

interface IntermediateAlbumInterface
{
    public function getArtist(): ?string;

    public function setArtist(?string $artist): IntermediateAlbumInterface;

    public function getTitle(): ?string;

    public function setTitle(?string $title): IntermediateAlbumInterface;

    /**
     * @return array<IntermediateSongInterface>
     */
    public function getSongs(): array;

    public function addSong(IntermediateSongInterface $song): IntermediateAlbumInterface;
}