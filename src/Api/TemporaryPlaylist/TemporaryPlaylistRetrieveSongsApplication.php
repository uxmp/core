<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

/**
 * Retrieves the temporary playlist of a user
 */
final class TemporaryPlaylistRetrieveSongsApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly TemporaryPlaylistRepositoryInterface $temporaryPlaylistRepository,
        private readonly SongRepositoryInterface $songRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        // find existing playlist; if not available, create a new one
        $temporaryPlaylist = $this->temporaryPlaylistRepository->findOneBy([
            'owner' => $user,
            'id' => $args['temporaryPlaylistId'],
        ]);

        if ($temporaryPlaylist === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        $songs = [];
        foreach ($temporaryPlaylist->getSongList() as $songId) {
            $song = $this->songRepository->find($songId);

            if ($song !== null) {
                $songs[] = $this->resultItemFactory->createSongListItem(
                    $song,
                    $song->getAlbum()
                );
            }
        }

        return $this->asJson(
            $response,
            [
                'offset' => $temporaryPlaylist->getOffset(),
                'songs' => $songs,
            ]
        );
    }
}
