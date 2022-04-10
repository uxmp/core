<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Uxmp\Core\Orm\Repository\CatalogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatalogRepository::class)]
#[ORM\Table(name: 'catalog')]
class Catalog implements CatalogInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $path = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): CatalogInterface
    {
        $this->path = $path;
        return $this;
    }
}
