<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\AlbumRepository")
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
     * @Column(type="integer")
     */
    private int $catalog_id;

    /**
     * @Column(type="string", length="32", nullable="true", unique=true)
     */
    private ?string $mbid = null;

    /**
     * @ManyToOne(targetEntity="Artist", inversedBy="albums")
     * @JoinColumn(name="artist_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ArtistInterface $artist;

    /**
     * @ManyToOne(targetEntity="Catalog")
     * @JoinColumn(name="catalog_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private CatalogInterface $catalog;

    /**
     * @OneToMany(targetEntity="Disc", mappedBy="album", cascade={"ALL"}, indexBy="id")
     *
     * @var ArrayCollection<int, DiscInterface>
     */
    private iterable $discs;

    #[Pure]
    public function __construct()
    {
        $this->discs = new ArrayCollection();
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

    public function getArtist(): ArtistInterface
    {
        return $this->artist;
    }

    public function setArtist(ArtistInterface $artist): AlbumInterface
    {
        $this->artist = $artist;
        return $this;
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

    public function getDiscs(): iterable
    {
        return $this->discs;
    }

    public function getDiscCount(): int
    {
        return $this->discs->count();
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): AlbumInterface
    {
        $this->catalog = $catalog;
        return $this;
    }
}
