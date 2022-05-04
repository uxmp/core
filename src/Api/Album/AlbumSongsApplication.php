<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumSongsApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory
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

        $discsData = [];

        foreach ($album->getDiscs() as $disc) {
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
                'number' => $disc->getNumber(),
            ];
        }

        return $this->asJson(
            $response,
            ['items' => $discsData]
        );
    }
}
