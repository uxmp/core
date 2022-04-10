<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Repository\DiscRepository;

#[ORM\Entity(repositoryClass: DiscRepository::class)]
#[ORM\Table(name: 'disc')]
#[ORM\UniqueConstraint(name: 'mbid_discnumber', columns: ['mbid', 'number'])]
class Disc implements DiscInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $album_id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $number;

    #[ORM\Column(type: Types::STRING, length: 32, nullable: true)]
    private ?string $mbid = null;

    #[ORM\Column(type: Types::INTEGER, length: 6)]
    private int $length = 0;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'discs')]
    #[ORM\JoinColumn(name: 'album_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private AlbumInterface $album;

    /**
     * @var Collection<int, SongInterface>
     */
    #[ORM\OneToMany(mappedBy: 'disc', targetEntity: Song::class, cascade: ['ALL'], indexBy: 'id')]
    private Collection $songs;

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

    public function addSong(SongInterface $song): DiscInterface
    {
        $this->songs->add($song);
        return $this;
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

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): DiscInterface
    {
        $this->length = $length;
        return $this;
    }
}
