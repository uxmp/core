<?php

namespace Usox\Core\Component\Tag\Container;

interface IntermediateSongInterface
{
    public function getTrackNumber(): ?int;

    public function setTrackNumber(?int $track_number): IntermediateSongInterface;

    public function getFilename(): ?string;

    public function setFilename(?string $filename): IntermediateSongInterface;

    public function getTitle(): ?string;

    public function setTitle(?string $title): IntermediateSongInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): IntermediateSongInterface;
}