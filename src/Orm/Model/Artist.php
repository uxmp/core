<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\Pure;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\ArtistRepository;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table(name: 'artist')]
class Artist implements ArtistInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true, nullable: true)]
    private ?string $mbid = null;

    /**
     * @var Collection<int, AlbumInterface>
     */
    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Album::class, cascade: ['ALL'], indexBy: 'id')]
    private Collection $albums;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
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
