<?php

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Orm\Model\AlbumInterface;

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
