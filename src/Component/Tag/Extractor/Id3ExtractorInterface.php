<?php

namespace Usox\Core\Component\Tag\Extractor;

interface Id3ExtractorInterface
{
    /**
     * @param array<mixed> $data
     * @return array{filename: string artist: string, album: string, title: string, track: int, id: string}
     */
    public function extract(
        string $filename,
        array $data
    ): array;
}