<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\SongRepository;

#[ORM\Entity(repositoryClass: SongRepository::class)]
#[ORM\Table(name: 'song')]
class Song implements SongInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $title = '';

    #[ORM\Column(type: Types::INTEGER)]
    private int $track_number = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $artist_id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $disc_id;

    #[ORM\Column(type: Types::TEXT)]
    private string $filename;

    #[ORM\Column(type: Types::INTEGER)]
    private int $catalog_id;

    #[ORM\Column(type: Types::STRING, length: 32, unique: true, nullable: true)]
    private ?string $mbid = null;

    #[ORM\Column(type: Types::INTEGER, length: 5)]
    private int $length = 0;

    #[ORM\Column(type: Types::INTEGER, length: 4, nullable: true)]
    private ?int $year = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\ManyToOne(targetEntity: Disc::class, inversedBy: 'songs')]
    #[ORM\JoinColumn(name: 'disc_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DiscInterface $disc;

    #[ORM\ManyToOne(targetEntity: Artist::class)]
    #[ORM\JoinColumn(name: 'artist_id', referencedColumnName: 'id')]
    private ArtistInterface $artist;

    #[ORM\ManyToOne(targetEntity: Catalog::class)]
    #[ORM\JoinColumn(name: 'catalog_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
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

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): SongInterface
    {
        $this->year = $year;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): SongInterface
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getAlbum(): AlbumInterface
    {
        return $this->getDisc()->getAlbum();
    }
}
