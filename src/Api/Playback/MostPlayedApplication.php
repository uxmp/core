<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Retrieve the list of mosty played songs
 */
final class MostPlayedApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
        private readonly SongRepositoryInterface $songRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $mostPlayed = $this->playbackHistoryRepository->getMostPlayed();

        $result = [];

        foreach ($mostPlayed as $item) {
            $song = $this->songRepository->find($item['song_id']);
            if ($song === null) {
                continue;
            }

            $result[] = [
                'count' => $item['cnt'],
                'song' => $this->resultItemFactory->createSongListItem($song, $song->getAlbum()),
            ];
        }

        return $this->asJson(
            $response,
            ['items' => $result],
        );
    }
}
