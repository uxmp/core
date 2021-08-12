<?php

declare(strict_types=1);

namespace Usox\Core\Api\Random;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

final class RandomSongsApplication extends AbstractApiApplication
{
    public function __construct(
        private SongRepositoryInterface $songRepository
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        foreach ($this->songRepository->findAll() as $song) {
            $album = $song->getDisc()->getAlbum();

            $list[] = [
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

        shuffle($list);

        $response->getBody()->write(
            (string) json_encode(['items' => $list], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-Type', 'application/json');
    }
}
