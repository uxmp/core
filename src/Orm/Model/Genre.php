<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\GenreRepository;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ORM\Table(name: 'genre')]
class Genre implements GenreInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $title = '';

    /** @var Collection<int, GenreMapInterface> */
    #[ORM\OneToMany(mappedBy: 'genre_map', targetEntity: GenreMap::class, cascade: ['ALL'])]
    private Collection $mapped_genres;

    public function __construct()
    {
        $this->mapped_genres = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): GenreInterface
    {
        $this->title = $title;
        return $this;
    }
}
