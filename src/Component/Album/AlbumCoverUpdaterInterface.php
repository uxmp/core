<?php

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Orm\Model\AlbumInterface;

interface AlbumCoverUpdaterInterface
{
    /**
     * @param array{
     *   comments: array{
     *     picture: array<array{image_mime: string, data: string}>
     *   }
     * } $metadata
     */
    public function update(
        AlbumInterface $album,
        array $metadata
    ): void;
}
