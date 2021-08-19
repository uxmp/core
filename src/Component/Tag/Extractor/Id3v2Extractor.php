<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

final class Id3v2Extractor implements ExtractorInterface
{
    public function extract(
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $tags = $data['id3v2'];

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
        return array_key_exists('id3v2', $data);
    }
}
