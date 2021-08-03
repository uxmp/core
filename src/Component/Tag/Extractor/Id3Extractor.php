<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Extractor;

final class Id3Extractor implements Id3ExtractorInterface
{
    public function extract(
        string $filename,
        array $data
    ): array {
        return [
            'filename' => $filename,
            'artist' => current($data['artist']),
            'album' => current($data['album']),
            'title' => current($data['title']),
            'track' => (int) current($data['track_number']),
            'id' => md5($filename),
        ];
    }
}