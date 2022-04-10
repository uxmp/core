<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Uxmp\Core\Orm\Repository\AlbumRepository;

#[ORM\Table(name: 'album')]
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album implements AlbumInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $artist_id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $catalog_id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $last_modified;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true, nullable: true)]
    private ?string $mbid = null;

    #[ORM\ManyToOne(targetEntity: Artist::class, inversedBy: 'albums')]
    #[ORM\JoinColumn(name: 'artist_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ArtistInterface $artist;

    #[ORM\ManyToOne(targetEntity: Catalog::class)]
    #[ORM\JoinColumn(name: 'catalog_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private CatalogInterface $catalog;

    /**
     * @var ArrayCollection<int, DiscInterface>
     */
    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Disc::class, cascade: ['ALL'], indexBy: 'id')]
    #[ORM\OrderBy(['number' => 'ASC'])]
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

    public function getLength(): int
    {
        return array_sum(
            array_map(
                fn (DiscInterface $disc): int => $disc->getLength(),
                $this->discs->toArray()
            )
        );
    }

    public function getLastModified(): DateTimeInterface
    {
        return $this->last_modified;
    }

    public function setLastModified(DateTimeInterface $last_modified): AlbumInterface
    {
        $this->last_modified = $last_modified;
        return $this;
    }

    #[Pure]
    public function getArtItemType(): string
    {
        return 'album';
    }

    #[Pure]
    public function getArtItemId(): ?string
    {
        return $this->getMbid();
    }

    public function getType(): string
    {
        return $this->getArtItemType();
    }
}
