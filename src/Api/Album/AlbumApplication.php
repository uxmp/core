<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\GenreMapRepositoryInterface;

final class AlbumApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ConfigProviderInterface $config,
        private readonly GenreMapRepositoryInterface $genreMapRepository,
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

        $artist = $album->getArtist();
        $genres = [];

        foreach ($this->genreMapRepository->findByAlbum($album) as $mapped_genre) {
            $genres[] = [
                'id' => $mapped_genre->getGenreId(),
                'title' => $mapped_genre->getGenreTitle(),
            ];
        }

        return $this->asJson(
            $response,
            [
                'id' => $albumId,
                'name' => $album->getTitle(),
                'artistId' => $artist->getId(),
                'artistName' => $artist->getTitle(),
                'cover' => sprintf('%s/art/album/%d', $this->config->getBaseUrl(), $albumId),
                'length' => $album->getLength(),
                'mbId' => $album->getMbid(),
                'genres' => $genres,
            ]
        );
    }
}
