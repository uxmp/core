<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $albumId = (int) ($args['albumId'] ?? 0);

        $album = $this->albumRepository->find($albumId);

        if ($album === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        $discs = $album->getDiscs();
        $discsData = [];

        $artist = $album->getArtist();
        $albumId = $album->getId();
        $albumName = $album->getTitle();
        $artistId = $artist->getId();
        $artistName = $artist->getTitle();

        $baseUrl = $this->config->getBaseUrl();
        $cover = sprintf('%s/art/album/%s', $baseUrl, $album->getMbid());

        foreach ($discs as $disc) {
            $songData = [];

            foreach ($disc->getSongs() as $song) {
                $songId = $song->getId();

                $songData[] = [
                    'id' => $songId,
                    'name' => $song->getTitle(),
                    'artistName' => $artistName,
                    'albumName' => $albumName,
                    'trackNumber' => $song->getTrackNumber(),
                    'playUrl' => sprintf('%s/play/%d', $baseUrl, $songId),
                    'cover' => $cover,
                    'artistId' => $artistId,
                    'albumId' => $albumId,
                ];
            }

            $discsData[] = [
                'id' => $disc->getId(),
                'songs' => $songData,
            ];
        }

        return $this->asJson(
            $response,
            [
                'id' => $albumId,
                'name' => $albumName,
                'artistId' => $artistId,
                'artistName' => $artistName,
                'discs' => $discsData,
                'cover' => $cover,
            ]
        );
    }
}
