<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Retrieve all playlists which belong to the current user
 */
final class PlaylistListByUserApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
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
                'items' => array_map(
                    fn (PlaylistInterface $playlist): JsonSerializable => $this->resultItemFactory->createPlaylistItem($playlist),
                    $this->playlistRepository->findBy(
                        ['owner' => $request->getAttribute(SessionValidatorMiddleware::USER)],
                        ['name' => 'ASC']
                    )
                ),
            ]
        );
    }
}
