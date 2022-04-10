<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition\Handler;

interface HandlerInterface
{
    /**
     * Adds all eligible song ids to the songList array
     *
     * @param array<int> $songList
     */
    public function handle(int $mediaId, array &$songList): void;
}
