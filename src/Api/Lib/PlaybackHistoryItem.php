<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\PlaybackHistoryInterface;

final class PlaybackHistoryItem implements JsonSerializable
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly PlaybackHistoryInterface $playbackHistory,
    ) {
    }

    /**
     * @return array{
     *  id: int,
     *  name: string,
     *  artistName: string|null,
     *  albumName: string|null,
     *  trackNumber: int,
     *  playUrl: string,
     *  cover: string,
     *  artistId: int,
     *  albumId: int,
     *  length: int,
     *  userId: int,
     *  userName: string
     * }
     */
    public function jsonSerialize(): array
    {
        $song = $this->playbackHistory->getSong();
        $album = $song->getDisc()->getAlbum();
        $user = $this->playbackHistory->getUser();
        $artist = $song->getArtist();

        $songId = $song->getId();
        $albumId = $album->getId();
        $baseUrl = $this->config->getBaseUrl();

        return [
            'id' => $songId,
            'name' => $song->getTitle(),
            'artistName' => $artist->getTitle(),
            'albumName' => $album->getTitle(),
            'trackNumber' => $song->getTrackNumber(),
            'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
            'cover' => sprintf('%s/art/album/%d', $baseUrl, $albumId),
            'artistId' => $artist->getId(),
            'albumId' => $albumId,
            'length' => $song->getLength(),
            'userId' => $user->getId(),
            'userName' => $user->getName(),
        ];
    }
}
