<?php

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;

interface AlbumCacheInterface
{
    /**
     * @param array<mixed> $analysisResult
     */
    public function retrieve(
        CatalogInterface $catalog,
        AudioFileInterface $audioFile,
        array $analysisResult
    ): AlbumInterface;
}
