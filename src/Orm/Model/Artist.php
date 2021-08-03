<?php

declare(strict_types=1);

namespace Usox\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity(repositoryClass="\Usox\Core\Orm\Repository\ArtistRepository")
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
     * @OneToMany(targetEntity="Album", mappedBy="artist", cascade={"ALL"}, indexBy="id")
     *
     * @var iterable<AlbumInterface>
     */
    private iterable $albums;

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
}