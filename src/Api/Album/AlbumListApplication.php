<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumListApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ConfigProviderInterface $config
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
        $baseUrl = $this->config->getBaseUrl();

        foreach ($this->albumRepository->findBy($condition, ['title' => 'ASC']) as $album) {
            $artist = $album->getArtist();

            $list[] = [
                'id' => $album->getId(),
                'artistId' => $artist->getId(),
                'artistName' => $artist->getTitle(),
                'name' => $album->getTitle(),
                'cover' => sprintf('%s/art/album/%s', $baseUrl, $album->getMbid()),
                'length' => $album->getLength(),
            ];
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
