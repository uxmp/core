<?php

namespace Uxmp\Core\Component\Tag\Extractor;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

interface ExtractorInterface
{
    /**
     * @param array<mixed> $data
     */
    public function extract(
        array $data,
        AudioFileInterface $audioFile
    ): void;

    /**
     * @param array<mixed> $data
     */
    public function applies(array $data): bool;
}
