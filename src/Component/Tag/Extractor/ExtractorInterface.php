<?php

namespace Usox\Core\Component\Tag\Extractor;

use Usox\Core\Component\Tag\Container\AudioFileInterface;

interface ExtractorInterface
{
    /**
     * @param array<mixed> $data
     */
    public function extract(
        string $filename,
        array $data,
        AudioFileInterface $audioFile
    ): void;

    /**
     * @param array<mixed> $data
     */
    public function applies(array $data): bool;
}
