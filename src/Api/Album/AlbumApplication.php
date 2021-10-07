<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ConfigProviderInterface $config,
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

        return $this->asJson(
            $response,
            [
                'id' => $albumId,
                'name' => $album->getTitle(),
                'artistId' => $artist->getId(),
                'artistName' => $artist->getTitle(),
                'cover' => sprintf('%s/art/album/%d', $this->config->getBaseUrl(), $albumId),
                'length' => $album->getLength()
            ]
        );
    }
}
