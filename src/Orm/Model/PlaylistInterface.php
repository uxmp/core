<?php

namespace Uxmp\Core\Orm\Model;

use Uxmp\Core\Component\Playlist\PlaylistTypeEnum;
use Uxmp\Core\Component\User\OwnerProviderInterface;

interface PlaylistInterface extends OwnerProviderInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): PlaylistInterface;

    public function setOwner(UserInterface $owner): PlaylistInterface;

    /**
     * @return array<int>
     */
    public function getSongList(): array;

    /**
     * @param array<int> $songList
     */
    public function setSongList(array $songList): PlaylistInterface;

    public function getSongCount(): int;

    public function setSongCount(int $songCount): PlaylistInterface;

    public function getType(): PlaylistTypeEnum;

    public function isStatic(): bool;

    public function setType(PlaylistTypeEnum $type): PlaylistInterface;

    /**
     * Updates the song list and also sets the song count
     *
     * @param array<int> $songList
     */
    public function updateSongList(
        array $songList
    ): PlaylistInterface;
}
