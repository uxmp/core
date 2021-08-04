<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="\Usox\Core\Orm\Repository\AlbumRepository")
 * @Table(name="album")
 */
class Album implements AlbumInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="string", nullable=true)
     */
    private ?string $title = null;

    /**
     * @Column(type="integer")
     */
    private int $artist_id;

    /**
     * @Column(type="string", length="32", nullable="true")
     */
    private ?string $mbid = null;

    /**
     * @ManyToOne(targetEntity="Artist", inversedBy="albums")
     * @JoinColumn(name="artist_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ArtistInterface $artist;

    /**
     * @OneToMany(targetEntity="Song", mappedBy="album", cascade={"ALL"}, indexBy="id")
     *
     * @var iterable<SongInterface>
     */
    private iterable $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): AlbumInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getArtistId(): int
    {
        return $this->artist_id;
    }

    public function setArtistId(int $artist_id): AlbumInterface
    {
        $this->artist_id = $artist_id;
        return $this;
    }

    public function getArtist(): ArtistInterface
    {
        return $this->artist;
    }

    public function setArtist(ArtistInterface $artist): AlbumInterface
    {
        $this->artist = $artist;
        return $this;
    }

    public function getSongs(): iterable
    {
        return $this->songs;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): AlbumInterface
    {
        $this->mbid = $mbid;
        return $this;
    }
}
