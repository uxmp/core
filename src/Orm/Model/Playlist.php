<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Component\Playlist\PlaylistTypeEnum;
use Uxmp\Core\Orm\Repository\PlaylistRepository;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
#[ORM\Table(name: 'playlist')]
class Playlist implements PlaylistInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    private string $name = '';

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $owner_user_id = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $song_count = 0;

    #[ORM\Column(type: Types::INTEGER, enumType: PlaylistTypeEnum::class, options: ['default' => PlaylistTypeEnum::STATIC])]
    private PlaylistTypeEnum $type = PlaylistTypeEnum::STATIC;

    /** @var array<int> */
    #[ORM\Column(type: Types::JSON, options: ['default' => '[]'])]
    private array $song_list = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
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

    /**
     * @return array<int>
     */
    public function getSongList(): array
    {
        return $this->song_list;
    }

    /**
     * @param array<int> $songList
     */
    public function setSongList(array $songList): PlaylistInterface
    {
        $this->song_list = $songList;
        return $this;
    }

    public function getSongCount(): int
    {
        return $this->song_count;
    }

    public function setSongCount(int $songCount): PlaylistInterface
    {
        $this->song_count = $songCount;
        return $this;
    }

    public function getType(): PlaylistTypeEnum
    {
        return $this->type;
    }

    public function isStatic(): bool
    {
        return $this->getType() === PlaylistTypeEnum::STATIC;
    }

    public function setType(PlaylistTypeEnum $type): PlaylistInterface
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Updates the song list and also sets the song count
     *
     * @param array<int> $songList
     */
    public function updateSongList(
        array $songList
    ): PlaylistInterface {
        $this->setSongList($songList);
        $this->setSongCount(count($songList));

        return $this;
    }
}
