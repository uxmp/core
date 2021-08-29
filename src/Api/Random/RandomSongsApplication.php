<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Random;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class RandomSongsApplication extends AbstractApiApplication
{
    private const DEFAULT_LIMIT = 100;

    public function __construct(
        private SongRepositoryInterface $songRepository,
        private ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];
        $baseUrl = $this->config->getBaseUrl();

        $limit = (int) ($args['limit'] ?? self::DEFAULT_LIMIT);

        foreach ($this->songRepository->findAll() as $song) {
            $album = $song->getDisc()->getAlbum();

            $artist = $album->getArtist();
            $songId = $song->getId();

            $list[] = [
                'id' => $songId,
                'name' => $song->getTitle(),
                'artistName' => $artist->getTitle(),
                'albumName' => $album->getTitle(),
                'trackNumber' => $song->getTrackNumber(),
                'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
                'cover' => sprintf('%s/art/album/%s', $baseUrl, $album->getMbid()),
                'artistId' => $artist->getId(),
                'albumId' => $album->getId()
            ];
        }

        // @todo inefficient, but doctrine doesn't support RAND order natively
        shuffle($list);

        return $this->asJson(
            $response,
            ['items' => array_slice($list, 0, $limit)]
        );
    }
}
