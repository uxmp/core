<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Extractor;

use Usox\Core\Component\Tag\Container\AudioFileInterface;

final class Id3v2Extractor implements ExtractorInterface
{
    public function extract(
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $tags = $data['id3v2'];

        $audioFile
            ->setMbid($tags['text']['MusicBrainz Release Track Id'] ?? null)
            ->setTitle(current($tags['title']))
            ->setTrackNumber((int) current($tags['track_number']))
            ->setArtistTitle(current($tags['artist']))
            ->setArtistMbid($tags['text']['MusicBrainz Album Artist Id'] ?? null)
            ->setAlbumTitle(current($tags['album']))
            ->setAlbumMbid($tags['text']['MusicBrainz Album Id'] ?? null)
            ->setDiscMbid($tags['text']['MusicBrainz Release Group Id'] ?? null)
            ->setDiscNumber((int) strstr((string) current($tags['part_of_a_set']), '/', true));
        ;
    }

    public function applies(array $data): bool
    {
        return array_key_exists('id3v2', $data);
    }
}
