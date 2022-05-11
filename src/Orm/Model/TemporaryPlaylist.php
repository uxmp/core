<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepository;

#[ORM\Entity(repositoryClass: TemporaryPlaylistRepository::class)]
#[ORM\Table(name: 'temporary_playlist')]
class TemporaryPlaylist implements TemporaryPlaylistInterface
{
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $owner_user_id = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $song_count = 0;

    /** @var array<int> */
    #[ORM\Column(type: Types::JSON, options: ['default' => '[]'])]
    private array $song_list = [];

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $offset = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private UserInterface $owner;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): TemporaryPlaylistInterface
    {
        $this->id = $id;
        return $this;
    }

    public function getOwner(): UserInterface
    {
        return $this->owner;
    }

    public function setOwner(UserInterface $owner): TemporaryPlaylistInterface
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
    public function setSongList(array $songList): TemporaryPlaylistInterface
    {
        $this->song_list = $songList;
        return $this;
    }

    public function getSongCount(): int
    {
        return $this->song_count;
    }

    public function setSongCount(int $songCount): TemporaryPlaylistInterface
    {
        $this->song_count = $songCount;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): TemporaryPlaylistInterface
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Updates the song list and also sets the song count
     *
     * @param array<int> $songList
     */
    public function updateSongList(
        array $songList
    ): TemporaryPlaylistInterface {
        $this->setSongList($songList);
        $this->setSongCount(count($songList));

        return $this;
    }
}
