<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtistApplication extends AbstractApiApplication
{
    public function __construct(
        private ArtistRepositoryInterface $artistRepository,
        private ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $artistId = (int) ($args['artistId'] ?? 0);

        $artist = $this->artistRepository->find($artistId);

        if ($artist === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        return $this->asJson(
            $response,
            [
                'id' => $artistId,
                'name' => $artist->getTitle(),
                'cover' => sprintf('%s/art/artist/%d', $this->config->getBaseUrl(), $artistId),
            ]
        );
    }
}
