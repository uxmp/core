<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;

final class VorbisExtractor implements ExtractorInterface
{
    private const META_DATA_KEY = 'vorbiscomment';

    public function extract(
        array $data,
        AudioFileInterface $audioFile
    ): void {
        $tags = $data[static::META_DATA_KEY];

        $audioFile
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
        return array_key_exists(static::META_DATA_KEY, $data);
    }
}
