<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;

/**
 * Returns three dictionaries containing information on a users favorites.
 */
final class FavoriteListApplication extends AbstractApiApplication
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
