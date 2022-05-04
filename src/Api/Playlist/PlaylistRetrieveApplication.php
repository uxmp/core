<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Retrieves a playlist
 */
final class PlaylistRetrieveApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $playlistId = (int) ($args['playlistId'] ?? 0);

        $playlist = $this->playlistRepository->find($playlistId);

        if ($playlist === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        return $this->asJson(
            $response,
            [
                'id' => $playlist->getId(),
                'name' => $playlist->getName(),
            ]
        );
    }
}
