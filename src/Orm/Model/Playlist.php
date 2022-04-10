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
     * @Column(type="integer", options={"default" : 0})
     */
    private int $owner_user_id = 0;

    /**
     * @Column(type="integer", options={"default" : 0})
     */
    private int $song_count = 0;

    /**
     * @Column(type="json", options={"default" : "[]"})
     *
     * @var array<int>
     */
    private array $song_list = [];

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
