<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Extractor;

use Usox\Core\Component\Tag\Container\AudioFileInterface;

final class Id3Extractor implements Id3ExtractorInterface
{
    public function extract(
        string $filename,
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $audioFile
            ->setFilename($filename)
            ->setMbid($data['text']['MusicBrainz Release Track Id'] ?? null)
            ->setTitle(current($data['title']))
            ->setTrackNumber((int) current($data['track_number']))
            ->setArtistTitle(current($data['artist']))
            ->setArtistMbid($data['text']['MusicBrainz Album Artist Id'] ?? null)
            ->setAlbumTitle(current($data['album']))
            ->setAlbumMbid($data['text']['MusicBrainz Album Id'] ?? null)
        ;
    }
}
