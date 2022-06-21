<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use JsonSerializable;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;

/**
 * Defines single result items for album lists
 */
final class AlbumListItem implements JsonSerializable
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly AlbumInterface $album,
    ) {
    }

    /**
     * @return array{
     *  id: int,
     *  artistId: int,
     *  artistName: string,
     *  name: string,
     *  cover: string,
     *  length: int
     * }
     */
    public function jsonSerialize(): array
    {
        $artist = $this->album->getArtist();
        $albumId = $this->album->getId();

        $baseUrl = $this->config->getBaseUrl();

        return [
            'id' => $albumId,
            'artistId' => $artist->getId(),
            'artistName' => (string) $artist->getTitle(),
            'name' => (string) $this->album->getTitle(),
            'cover' => sprintf('%s/art/album/%d', $baseUrl, $albumId),
            'length' => $this->album->getLength(),
        ];
    }
}
