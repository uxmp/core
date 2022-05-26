<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Uxmp\Core\Orm\Repository\GenreMapRepository;

#[ORM\Entity(repositoryClass: GenreMapRepository::class)]
#[ORM\Table(name: 'genre_map')]
class GenreMap implements GenreMapInterface
{
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'CUSTOM'), ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private string $id;

    #[ORM\Column(type: Types::STRING, enumType: GenreMapEnum::class)]
    private GenreMapEnum $mapped_item_type;

    #[ORM\Column(type: Types::INTEGER)]
    private int $mapped_item_id = 0;

    #[ORM\Column(type: Types::INTEGER)]
    private int $genre_id = 0;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: 'mapped_genres')]
    #[ORM\JoinColumn(name: 'genre_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private GenreInterface $genre;

    public function getId(): string
    {
        return $this->id;
    }

    public function getGenreTitle(): string
    {
        return $this->genre->getTitle();
    }

    public function getMappedItemType(): GenreMapEnum
    {
        return $this->mapped_item_type;
    }

    public function setMappedItemType(GenreMapEnum $value): GenreMapInterface
    {
        $this->mapped_item_type = $value;
        return $this;
    }

    public function getMappedItemId(): int
    {
        return $this->mapped_item_id;
    }

    public function setMappedItemId(int $mapped_item_id): GenreMapInterface
    {
        $this->mapped_item_id = $mapped_item_id;
        return $this;
    }

    public function setGenre(GenreInterface $genre): GenreMapInterface
    {
        $this->genre = $genre;
        return $this;
    }

    public function getGenre(): GenreInterface
    {
        return $this->genre;
    }
}
