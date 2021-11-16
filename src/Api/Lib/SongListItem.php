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
        private ConfigProviderInterface $config,
        private SongInterface $song,
        private AlbumInterface $album
    ) {
    }

    public function jsonSerialize()
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
        ];
    }
}
