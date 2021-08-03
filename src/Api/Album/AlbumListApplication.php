<?php

declare(strict_types=1);

namespace Usox\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Orm\Model\AlbumInterface;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumListApplication extends AbstractApiApplication
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
        $list = [];

        foreach ($this->albumRepository->findAll() as $album) {
            /** @var AlbumInterface $album */

            $songList = [];
            foreach ($album->getSongs() as $track) {
                $songList[] = sprintf('http://localhost:8888/play/'.$track->getId());
            }

            $list[] = [
                'id' => $album->getId(),
                'artistId' => $album->getArtistId(),
                'name' => $album->getTitle(),
                'songList' => $songList
            ];
        }

        $response->getBody()->write(
            json_encode(['items' => $list], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-Type', 'application/json');
    }
}