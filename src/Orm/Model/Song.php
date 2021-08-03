<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Model;

/**
 * @Entity(repositoryClass="\Usox\Core\Orm\Repository\SongRepository")
 * @Table(name="song")
 */
class Song implements SongInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="string")
     */
    private string $title = '';

    /**
     * @Column(type="integer")
     */
    private int $track_number = 0;

    /**
     * @Column(type="integer")
     */
    private int $artist_id;

    /**
     * @Column(type="integer")
     */
    private int $album_id;

    /**
     * @Column(type="text")
     */
    private string $filename;

    /**
     * @ManyToOne(targetEntity="Album", inversedBy="songs")
     * @JoinColumn(name="album_id", referencedColumnName="id")
     */
    private AlbumInterface $album;

    /**
     * @ManyToOne(targetEntity="Artist")
     * @JoinColumn(name="artist_id", referencedColumnName="id")
     */
    private ArtistInterface $artist;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SongInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getArtistId(): int
    {
        return $this->artist_id;
    }

    public function setArtistId(int $artist_id): SongInterface
    {
        $this->artist_id = $artist_id;
        return $this;
    }

    public function getArtist(): ArtistInterface
    {
        return $this->artist;
    }

    public function setArtist(ArtistInterface $artist): SongInterface
    {
        $this->artist = $artist;
        return $this;
    }

    public function getTrackNumber(): int
    {
        return $this->track_number;
    }

    public function setTrackNumber(int $track_number): SongInterface
    {
        $this->track_number = $track_number;
        return $this;
    }

    public function getAlbum(): AlbumInterface
    {
        return $this->album;
    }

    public function setAlbum(AlbumInterface $album): SongInterface
    {
        $this->album = $album;
        return $this;
    }

    public function getAlbumId(): int
    {
        return $this->album_id;
    }

    public function setAlbumId(int $album_id): SongInterface
    {
        $this->album_id = $album_id;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): SongInterface
    {
        $this->filename = $filename;
        return $this;
    }

}