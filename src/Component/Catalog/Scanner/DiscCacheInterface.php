<?php

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\DiscInterface;

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
