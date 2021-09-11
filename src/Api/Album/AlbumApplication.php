<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ConfigProviderInterface $config,
        private ResultItemFactoryInterface $resultItemFactory
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
        $artist = $album->getArtist();
        $discsData = [];

        foreach ($discs as $disc) {
            $songData = [];

            foreach ($disc->getSongs() as $song) {
                $songData[] = $this->resultItemFactory->createSongListItem(
                    $song,
                    $album
                );
            }

            $discsData[] = [
                'id' => $disc->getId(),
                'songs' => $songData,
                'length' => $disc->getLength(),
            ];
        }

        return $this->asJson(
            $response,
            [
                'id' => $album->getId(),
                'name' => $album->getTitle(),
                'artistId' => $artist->getId(),
                'artistName' => $artist->getTitle(),
                'discs' => $discsData,
                'cover' => sprintf('%s/art/album/%s', $this->config->getBaseUrl(), $album->getMbid()),
                'length' => array_sum(array_map(fn (array $data): int => $data['length'], $discsData))
            ]
        );
    }
}
