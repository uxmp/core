<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\ArtistRepository")
 * @Table(name="artist")
 */
class Artist implements ArtistInterface
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
     * @Column(type="string", length="32", nullable=true, unique=true)
     */
    private ?string $mbid = null;

    /**
     * @OneToMany(targetEntity="Album", mappedBy="artist", cascade={"ALL"}, indexBy="id")
     *
     * @var Collection<int, AlbumInterface>
     */
    private Collection $albums;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private \DateTimeInterface $last_modified;

    #[Pure]
    public function __construct()
    {
        $this->albums = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): ArtistInterface
    {
        $this->title = $title;
        return $this;
    }

    public function getAlbums(): iterable
    {
        return $this->albums;
    }

    public function addAlbum(AlbumInterface $album): ArtistInterface
    {
        $this->albums->add($album);
        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): ArtistInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

    #[Pure]
    public function getArtItemType(): string
    {
        return 'artist';
    }

    #[Pure]
    public function getArtItemId(): ?string
    {
        return $this->getMbid();
    }

    public function getLastModified(): ?\DateTimeInterface
    {
        return $this->last_modified;
    }

    public function setLastModified(\DateTimeInterface $last_modified): ArtistInterface
    {
        $this->last_modified = $last_modified;
        return $this;
    }
}
