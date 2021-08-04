<?php

declare(strict_types=1);

namespace Usox\Core\Component\Tag\Extractor;

use Usox\Core\Component\Tag\Container\AudioFileInterface;

final class VorbisExtractor implements ExtractorInterface
{
    public function extract(
        string $filename,
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $tags = $data['vorbiscomment'];

        $audioFile
            ->setFilename($filename)
            ->setMbid(current($tags['musicbrainz_trackid']))
            ->setTitle(current($tags['title']))
            ->setTrackNumber((int) current($tags['tracknumber']))
            ->setArtistTitle(current($tags['albumartist']))
            ->setArtistMbid(current($tags['musicbrainz_albumartistid']))
            ->setAlbumTitle(current($tags['album']))
            ->setAlbumMbid(current($tags['musicbrainz_albumid']))
            ->setDiscMbid(current($tags['musicbrainz_releasegroupid']))
            ->setDiscNumber((int) current($tags['discnumber']))
        ;
    }

    public function applies(array $data): bool
    {
        return array_key_exists('vorbiscomment', $data);
    }
}
