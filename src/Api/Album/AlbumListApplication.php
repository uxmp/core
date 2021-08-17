<?php

declare(strict_types=1);

namespace Usox\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
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

        foreach ($this->albumRepository->findBy([], ['title' => 'ASC']) as $album) {
            $list[] = [
                'albumId' => $album->getId(),
                'artistId' => $album->getArtist()->getId(),
                'artistName' => $album->getArtist()->getTitle(),
                'name' => $album->getTitle(),
                'cover' => sprintf('http://localhost:8888/art/album/%s', $album->getMbid())
            ];
        }

        $response->getBody()->write(
            (string) json_encode(['items' => $list], JSON_PRETTY_PRINT)
        );

        return $this->asJson($response);
    }
}
