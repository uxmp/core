<?php

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\ArtistInterface;

interface ArtistCacheInterface
{
    public function retrieve(AudioFileInterface $audioFile): ArtistInterface;
}
