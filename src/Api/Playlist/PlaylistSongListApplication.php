<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Playlist\PlaylistSongRetrieverInterface;
use Uxmp\Core\Orm\Model\SongInterface;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Playlist song list retrieval
 */
final class PlaylistSongListApplication extends AbstractApiApplication
{
    public function __construct(
        private PlaylistRepositoryInterface $playlistRepository,
        private PlaylistSongRetrieverInterface $playlistSongRetriever,
        private ResultItemFactoryInterface $resultItemFactory,
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

        $result = [];

        /** @var SongInterface $song */
        foreach ($this->playlistSongRetriever->retrieve($playlist) as $song) {
            $result[] = $this->resultItemFactory->createSongListItem($song, $song->getAlbum());
        }

        return $this->asJson(
            $response,
            [
                'items' => $result,
            ]
        );
    }
}
