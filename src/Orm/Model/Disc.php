<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\DiscRepository")
 * @Table(name="disc")
 */
class Disc implements DiscInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="integer")
     */
    private int $album_id;

    /**
     * @Column(type="integer")
     */
    private int $number;

    /**
     * @Column(type="string", length="32", nullable="true", unique=true)
     */
    private ?string $mbid = null;

    /**
     * @ManyToOne(targetEntity="Album", inversedBy="discs")
     * @JoinColumn(name="album_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private AlbumInterface $album;

    /**
     * @OneToMany(targetEntity="Song", mappedBy="disc", cascade={"ALL"}, indexBy="id")
     *
     * @var iterable<SongInterface>
     */
    private iterable $songs;

    #[Pure]
    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSongs(): iterable
    {
        return $this->songs;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): DiscInterface
    {
        $this->mbid = $mbid;
        return $this;
    }

    public function getAlbumId(): int
    {
        return $this->album_id;
    }

    public function setAlbumId(int $album_id): DiscInterface
    {
        $this->album_id = $album_id;
        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): DiscInterface
    {
        $this->number = $number;
        return $this;
    }

    public function getAlbum(): AlbumInterface
    {
        return $this->album;
    }

    public function setAlbum(AlbumInterface $album): DiscInterface
    {
        $this->album = $album;
        return $this;
    }
}
