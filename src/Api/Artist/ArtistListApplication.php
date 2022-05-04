<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtistListApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        foreach ($this->artistRepository->findBy([], ['title' => 'ASC']) as $artist) {
            $artistId = $artist->getId();

            $list[] = [
                'id' => $artistId,
                'name' => $artist->getTitle(),
                'cover' => sprintf('%s/art/artist/%d', $this->config->getBaseUrl(), $artistId),
            ];
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
