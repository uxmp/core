<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\PlaylistRepository")
 * @Table(name="playlist")
 */
class Playlist implements PlaylistInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="string")
     */
    private string $name = '';

    /**
     * @Column(type="integer")
     */
    private int $owner_user_id;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="owner_user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private UserInterface $owner;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): PlaylistInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function setOwner(UserInterface $owner): PlaylistInterface
    {
        $this->owner = $owner;
        return $this;
    }
}
