<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ArrayCollection<int, AlbumInterface>
     */
    private ArrayCollection $albums;

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
}
