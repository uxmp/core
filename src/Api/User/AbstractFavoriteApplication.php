<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Favorite\FavoriteAbleInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

abstract class AbstractFavoriteApplication extends AbstractApiApplication
{
    protected function __construct(
        private SongRepositoryInterface $songRepository
    ) {
    }

    /**
     * @param array<string, scalar> $args
     */
    protected function getItem(
        ServerRequestInterface $request,
        array $args
    ): ?FavoriteAbleInterface {
        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();

        $itemId = (int) ($body['itemId'] ?? 0);

        return match ((string) ($args['type'] ?? '')) {
            'song' => $this->songRepository->find($itemId),
            default => null,
        };
    }
}
