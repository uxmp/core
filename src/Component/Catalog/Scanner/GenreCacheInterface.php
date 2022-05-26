<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;

interface GenreCacheInterface
{
    public function enrich(
        AlbumInterface $album,
        AudioFileInterface $audioFile,
    ): void;
}
