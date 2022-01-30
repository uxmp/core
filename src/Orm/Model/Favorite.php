<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\FavoriteRepository")
 * @Table(name="favorite")
 */
class Favorite implements FavoriteInterface
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
    private int $user_id;

    /**
     * @Column(type="integer")
     */
    private int $item_id;

    /**
     * @Column(type="string")
     */
    private string $type;

    /**
     * @Column(type="datetime")
     */
    private \DateTimeInterface $date;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
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
