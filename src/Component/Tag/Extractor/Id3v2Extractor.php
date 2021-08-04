<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Extractor;

use Usox\Core\Component\Tag\Container\AudioFileInterface;

final class Id3v2Extractor implements ExtractorInterface
{
    public function extract(
        string $filename,
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $audioFile
            ->setFilename($filename)
            ->setMbid($data['id3v2']['text']['MusicBrainz Release Track Id'] ?? null)
            ->setTitle(current($data['id3v2']['title']))
            ->setTrackNumber((int) current($data['id3v2']['track_number']))
            ->setArtistTitle(current($data['id3v2']['artist']))
            ->setArtistMbid($data['id3v2']['text']['MusicBrainz Album Artist Id'] ?? null)
            ->setAlbumTitle(current($data['id3v2']['album']))
            ->setAlbumMbid($data['id3v2']['text']['MusicBrainz Album Id'] ?? null)
        ;
    }

    public function applies(array $data): bool
    {
        return array_key_exists('id3v2', $data);
    }
}
