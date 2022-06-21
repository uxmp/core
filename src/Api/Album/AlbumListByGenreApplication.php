<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

/**
 * Builds a result containing all albums items having a certain genre
 */
final class AlbumListByGenreApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly GenreRepositoryInterface $genreRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $genreId = (int) ($args['genreId'] ?? null);

        $genre = $this->genreRepository->find($genreId);
        if ($genre === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        $list = [];

        foreach ($this->albumRepository->findByGenre($genre) as $album) {
            $list[] = $this->resultItemFactory->createAlbumListItem($album);
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
