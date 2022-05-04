<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\SongInterface;

final class SongListItem implements JsonSerializable
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly SongInterface $song,
        private readonly AlbumInterface $album
    ) {
    }

    /**
     * @return array{
     *  id: int,
     *  name: string,
     *  artistName: null|string,
     *  albumName: null|string,
     *  trackNumber: int,
     *  playUrl: string,
     *  cover: string,
     *  artistId: int,
     *  albumId: int,
     *  length: int,
     *  year: int|null
     * }
     */
    public function jsonSerialize(): array
    {
        $songId = $this->song->getId();
        $albumId = $this->album->getId();
        $baseUrl = $this->config->getBaseUrl();

        $artist = $this->album->getArtist();

        return [
            'id' => $songId,
            'name' => $this->song->getTitle(),
            'artistName' => $artist->getTitle(),
            'albumName' => $this->album->getTitle(),
            'trackNumber' => $this->song->getTrackNumber(),
            'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
            'cover' => sprintf('%s/art/album/%d', $baseUrl, $albumId),
            'artistId' => $artist->getId(),
            'albumId' => $albumId,
            'length' => $this->song->getLength(),
            'year' => $this->song->getYear(),
        ];
    }
}
