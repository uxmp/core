<?php

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\ArtistInterface;

interface ArtistCacheInterface
{
    public function retrieve(AudioFileInterface $audioFile): ArtistInterface;
}
