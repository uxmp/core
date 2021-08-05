<?php

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\AlbumInterface;

interface AlbumCacheInterface
{
    public function retrieve(AudioFileInterface $audioFile): AlbumInterface;
}
