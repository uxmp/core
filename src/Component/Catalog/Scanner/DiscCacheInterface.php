<?php

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\DiscInterface;

interface DiscCacheInterface
{
    public function retrieve(AudioFileInterface $audioFile): DiscInterface;
}
