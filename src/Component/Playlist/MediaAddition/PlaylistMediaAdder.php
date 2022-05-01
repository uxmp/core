<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition;

use Uxmp\Core\Component\Playlist\MediaAddition\Handler\HandlerInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Adds songs of a certain media to a playlist
 */
final class PlaylistMediaAdder implements PlaylistMediaAdderInterface
{
    /**
     * @param array<string, HandlerInterface> $handlerList
     */
    public function __construct(
        private readonly array $handlerList,
        private readonly PlaylistRepositoryInterface $playlistRepository,
    ) {
    }

    /**
     * @throws Exception\InvalidMediaTypeException
     */
    public function add(
        PlaylistInterface $playlist,
        string $mediaType,
        int $mediaId
    ): PlaylistInterface {
        $handler = $this->handlerList[$mediaType] ?? null;
        if ($handler === null) {
            throw new Exception\InvalidMediaTypeException($mediaType);
        }

        // will be passed by reference to the handler
        $songList = [];

        $handler->handle($mediaId, $songList);

        // add songs
        $playlist->updateSongList(
            array_merge($playlist->getSongList(), $songList)
        );

        $this->playlistRepository->save($playlist);

        return $playlist;
    }
}
