<?php

namespace Usox\Core\Component\Album;

use Usox\Core\Orm\Model\AlbumInterface;

interface AlbumCoverUpdaterInterface
{
    /**
     * @param array<mixed> $metadata
     */
    public function update(
        AlbumInterface $album,
        array $metadata
    ): void;
}
