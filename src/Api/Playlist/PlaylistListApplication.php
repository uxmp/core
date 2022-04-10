<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Playlist list retrieval
 */
final class PlaylistListApplication extends AbstractApiApplication
{
    public function __construct(
        private PlaylistRepositoryInterface $playlistRepository,
        private ResultItemFactoryInterface $resultItemFactory,
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
                    $this->playlistRepository->findBy([], ['name' => 'ASC']),
                ),
            ]
        );
    }
}
