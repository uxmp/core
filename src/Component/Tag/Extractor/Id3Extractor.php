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
            'mbid' => $data['text']['MusicBrainz Release Track Id'] ?? null,
            'artist' => current($data['artist']),
            'artist_mbid' => $data['text']['MusicBrainz Album Artist Id'] ?? null,
            'album' => current($data['album']),
            'album_mbid' => $data['text']['MusicBrainz Album Id'] ?? null,
            'title' => current($data['title']),
            'track' => (int) current($data['track_number']),
            'id' => md5($filename),
        ];
    }
}
