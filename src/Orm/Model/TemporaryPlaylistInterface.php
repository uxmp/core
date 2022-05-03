<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

interface TemporaryPlaylistInterface
{
    public function getId(): string;

    public function getOwner(): UserInterface;

    public function setOwner(UserInterface $owner): TemporaryPlaylistInterface;

    /**
     * @return array<int>
     */
    public function getSongList(): array;

    /**
     * @param array<int> $songList
     */
    public function setSongList(array $songList): TemporaryPlaylistInterface;

    public function getSongCount(): int;

    public function setSongCount(int $songCount): TemporaryPlaylistInterface;

    public function getOffset(): int;

    public function setOffset(int $offset): TemporaryPlaylistInterface;

    /**
     * Updates the song list and also sets the song count
     *
     * @param array<int> $songList
     */
    public function updateSongList(array $songList): TemporaryPlaylistInterface;
}
