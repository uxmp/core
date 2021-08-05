<?php

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\DiscInterface;

interface DiscCacheInterface
{
    /**
     * @param array<mixed> $analysisResult
     */
    public function retrieve(
        AudioFileInterface $audioFile,
        array $analysisResult
    ): DiscInterface;
}
