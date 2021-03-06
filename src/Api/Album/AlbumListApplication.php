<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

/**
 * Returns all albums, optionally filtered by a single artist
 */
final class AlbumListApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $condition = [];
        $artistId = $args['artistId'] ?? null;

        if ($artistId !== null) {
            $condition['artist_id'] = (int) $artistId;
        }

        $list = [];

        foreach ($this->albumRepository->findBy($condition, ['title' => 'ASC']) as $album) {
            $list[] = $this->resultItemFactory->createAlbumListItem($album);
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
