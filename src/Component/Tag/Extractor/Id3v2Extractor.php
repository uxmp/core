<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

final class Id3v2Extractor implements ExtractorInterface
{
    private const META_DATA_KEY = 'id3v2';

    public function extract(
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $tags = $data[self::META_DATA_KEY];

        $audioFile
            ->setMbid($tags['text']['MusicBrainz Release Track Id'] ?? '')
            ->setTitle(current($tags['title']))
            ->setTrackNumber((int) current($tags['track_number']))
            ->setArtistTitle(current($tags['artist']))
            ->setArtistMbid($tags['text']['MusicBrainz Album Artist Id'] ?? '')
            ->setAlbumTitle(current($tags['album']))
            ->setAlbumMbid($tags['text']['MusicBrainz Album Id'] ?? '')
            ->setDiscMbid($tags['text']['MusicBrainz Release Group Id'] ?? '')
            ->setDiscNumber((int) strstr((string) current($tags['part_of_a_set']), '/', true))
        ;
    }

    public function applies(array $data): bool
    {
        return array_key_exists(self::META_DATA_KEY, $data);
    }
}
