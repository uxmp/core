<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;

final class FavoritesApplication extends AbstractApiApplication
{
    public function __construct(
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        return $this->asJson(
            $response,
            [
                'albums' => [],
                'songs' => [],
                'artists' => [],
            ]
        );
    }
}
