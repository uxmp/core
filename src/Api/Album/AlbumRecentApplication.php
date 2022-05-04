<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Album;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumRecentApplication extends AbstractApiApplication
{
    private const ALBUM_LIMIT = 10;

    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $baseUrl = $this->config->getBaseUrl();
        $list = [];

        foreach ($this->albumRepository->findBy([], ['last_modified' => 'DESC'], static::ALBUM_LIMIT) as $album) {
            $artist = $album->getArtist();

            $albumId = $album->getId();

            $list[] = [
                'id' => $albumId,
                'artistId' => $artist->getId(),
                'artistName' => $artist->getTitle(),
                'name' => $album->getTitle(),
                'cover' => sprintf('%s/art/album/%d', $baseUrl, $albumId),
                'length' => $album->getLength(),
            ];
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
