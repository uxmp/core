<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\MediaAddition;

use Uxmp\Core\Orm\Model\PlaylistInterface;

/**
 * Adds songs of a certain media to a playlist
 */
interface PlaylistMediaAdderInterface
{
    /**
     * @throws Exception\InvalidMediaTypeException
     */
    public function add(PlaylistInterface $playlist, string $mediaType, int $mediaId): PlaylistInterface;
}
