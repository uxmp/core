<?php

namespace Uxmp\Core\Orm\Model;

use Uxmp\Core\Component\Favorite\FavoriteAbleInterface;

interface SongInterface extends FavoriteAbleInterface
{
    public function getId(): int;

    public function getTitle(): string;

    public function setTitle(string $title): SongInterface;

    public function getArtist(): ArtistInterface;

    public function setArtist(ArtistInterface $artist): SongInterface;

    public function getTrackNumber(): int;

    public function setTrackNumber(int $track_number): SongInterface;

    public function getFilename(): string;

    public function setFilename(string $filename): SongInterface;

    public function getMbid(): ?string;

    public function setMbid(?string $mbid): SongInterface;

    public function getDisc(): DiscInterface;

    public function setDisc(DiscInterface $disc): SongInterface;

    public function getCatalog(): CatalogInterface;

    public function setCatalog(CatalogInterface $catalog): SongInterface;

    public function getLength(): int;

    public function setLength(int $length): SongInterface;

    public function getYear(): ?int;

    public function setYear(?int $year): SongInterface;

    public function getMimeType(): ?string;

    public function setMimeType(?string $mimeType): SongInterface;

    public function getAlbum(): AlbumInterface;
}
