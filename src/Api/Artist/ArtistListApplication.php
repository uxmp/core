<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Artist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtistListApplication extends AbstractApiApplication
{
    public function __construct(
        private ArtistRepositoryInterface $artistRepository
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        foreach ($this->artistRepository->findBy([], ['title' => 'ASC']) as $artist) {
            $list[] = [
                'id' => $artist->getId(),
                'name' => $artist->getTitle(),
            ];
        }

        return $this->asJson($response, ['items' => $list]);
    }
}
