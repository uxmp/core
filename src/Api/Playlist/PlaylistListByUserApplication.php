<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Retrieve all playlists which belong to the current user
 */
final class PlaylistListByUserApplication extends AbstractApiApplication
{
    public function __construct(
        private PlaylistRepositoryInterface $playlistRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $playlists = $this->playlistRepository->findBy(
            ['owner' => $request->getAttribute(SessionValidatorMiddleware::USER)],
            ['name' => 'ASC']
        );

        $result = array_map(
            static function (PlaylistInterface $playlist): array {
                $owner = $playlist->getOwner();

                return [
                    'id' => $playlist->getId(),
                    'name' => $playlist->getName(),
                    'user_name' => $owner->getName(),
                    'user_id' => $owner->getId(),
                ];
            },
            $playlists
        );

        return $this->asJson(
            $response,
            [
                'items' => $result,
            ]
        );
    }
}
