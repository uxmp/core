<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Playlist list retrieval
 */
final class PlaylistListApplication extends AbstractApiApplication
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
        $playlists = $this->playlistRepository->findBy([], ['name' => 'ASC']);

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
