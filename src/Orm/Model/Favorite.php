<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\FavoriteRepository;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ORM\Table(name: 'favorite')]
class Favorite implements FavoriteInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $user_id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $item_id;

    #[ORM\Column(type: Types::STRING)]
    private string $type;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private UserInterface $user;

    public function setUser(UserInterface $user): FavoriteInterface
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setItemId(int $itemId): FavoriteInterface
    {
        $this->item_id = $itemId;
        return $this;
    }

    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function setType(string $type): FavoriteInterface
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setDate(\DateTimeInterface $date): FavoriteInterface
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
