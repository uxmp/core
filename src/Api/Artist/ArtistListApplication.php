<?php

declare(strict_types=1);

namespace Usox\Core\Api\Artist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;

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

        foreach ($this->artistRepository->findAll() as $artist) {
            $list[] = [
                'id' => $artist->getId(),
                'name' => $artist->getTitle(),
            ];
        }

        $response->getBody()->write(
            (string) json_encode(['items' => $list], JSON_PRETTY_PRINT)
        );
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-Type', 'application/json');
    }
}
