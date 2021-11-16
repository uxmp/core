<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;

final class PlaybackHistoryApplication extends AbstractApiApplication
{
    private const HISTORY_LIMIT = 15;

    public function __construct(
        private PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $history = $this->playbackHistoryRepository->findBy(
            [],
            ['play_date' => 'DESC'],
            static::HISTORY_LIMIT
        );

        $result = [];

        foreach ($history as $item) {
            $song = $item->getSong();
            $user = $item->getUser();

            $result[] = [
                'songId' => $song->getId(),
                'songName' => $song->getTitle(),
                'songArtistName' => $song->getArtist()->getTitle(),
                'userId' => $user->getId(),
                'userName' => $user->getName(),
                'playDate' => $item->getPlayDate()->getTimestamp(),
            ];
        }

        return $this->asJson(
            $response,
            $result,
        );
    }
}
