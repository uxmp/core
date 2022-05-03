<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

/**
 * Retrieves the temporary playlist of a user
 */
final class TemporaryPlaylistRetrieveApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly TemporaryPlaylistRepositoryInterface $temporaryPlaylistRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        // find existing playlist; if not available, create a new one
        $temporaryPlaylist = $this->temporaryPlaylistRepository->findOneBy(['owner' => $user]);

        return $this->asJson(
            $response,
            [
                'result' => $temporaryPlaylist?->getId(),
            ]
        );
    }
}
