<?php

declare(strict_types=1);

namespace Usox\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository
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

        foreach ($discs as $disc) {
            $songData = [];

            foreach ($disc->getSongs() as $song) {
                $songData[] = [
                    'id' => $song->getId(),
                    'name' => $song->getTitle(),
                    'artistName' => $album->getArtist()->getTitle(),
                    'albumName' => $album->getTitle(),
                    'trackNumber' => $song->getTrackNumber(),
                    'playUrl' => sprintf('http://localhost:8888/play/%d', $song->getId()),
                    'cover' => sprintf('http://localhost:8888/art/album/%s', $album->getMbid()),
                    'artistId' => $album->getArtist()->getId(),
                    'albumId' => $album->getId()
                ];
            }

            $discsData[] = [
                'id' => $disc->getId(),
                'songs' => $songData,
            ];
        }

        $data = [
            'id' => $album->getId(),
            'name' => $album->getTitle(),
            'artistId' => $album->getArtist()->getId(),
            'artistName' => $album->getArtist()->getTitle(),
            'discs' => $discsData,
        ];

        $response->getBody()->write(
            (string) json_encode($data, JSON_PRETTY_PRINT)
        );

        return $this->asJson($response);
    }
}
