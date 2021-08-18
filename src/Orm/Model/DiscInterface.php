<?php

namespace Uxmp\Core\Orm\Model;

interface DiscInterface
{
    public function getId(): int;

    /**
     * @return iterable<SongInterface>
     */
    public function getSongs(): iterable;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): DiscInterface;

    public function getAlbumId(): int;

    public function setAlbumId(int $album_id): DiscInterface;

    public function getNumber(): int;

    public function setNumber(int $number): DiscInterface;

    public function getAlbum(): AlbumInterface;

    public function setAlbum(AlbumInterface $album): DiscInterface;
}
