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

        $artist = $album->getArtist();
        $albumId = $album->getId();
        $albumName = $album->getTitle();
        $artistId = $artist->getId();
        $artistName = $artist->getTitle();

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
                    'playUrl' => sprintf('http://localhost:8888/play/%d', $songId),
                    'cover' => sprintf('http://localhost:8888/art/album/%s', $album->getMbid()),
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
            ]
        );
    }
}
