<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\SongRepository")
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
    private int $disc_id;

    /**
     * @Column(type="text")
     */
    private string $filename;

    /**
     * @Column(type="integer")
     */
    private int $catalog_id;

    /**
     * @Column(type="string", length="32", nullable=true, unique=true)
     */
    private ?string $mbid = null;

    /**
     * @Column(type="integer", length="5")
     */
    private int $length = 0;

    /**
     * @ManyToOne(targetEntity="Disc", inversedBy="songs")
     * @JoinColumn(name="disc_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private DiscInterface $disc;

    /**
     * @ManyToOne(targetEntity="Artist")
     * @JoinColumn(name="artist_id", referencedColumnName="id")
     */
    private ArtistInterface $artist;

    /**
     * @ManyToOne(targetEntity="Catalog")
     * @JoinColumn(name="catalog_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private CatalogInterface $catalog;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): SongInterface
    {
        $this->title = $title;
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

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): SongInterface
    {
        $this->filename = $filename;
        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): SongInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

    public function getDisc(): DiscInterface
    {
        return $this->disc;
    }

    public function setDisc(DiscInterface $disc): SongInterface
    {
        $this->disc = $disc;
        return $this;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): SongInterface
    {
        $this->catalog = $catalog;
        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): SongInterface
    {
        $this->length = $length;
        return $this;
    }

    public function getType(): string
    {
        return 'song';
    }
}
