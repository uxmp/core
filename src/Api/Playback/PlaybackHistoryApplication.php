<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;

final class PlaybackHistoryApplication extends AbstractApiApplication
{
    private const HISTORY_LIMIT = 15;

    public function __construct(
        private PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
        private ResultItemFactoryInterface $resultItemFactory,
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
            $result[] = $this->resultItemFactory->createPlaybackHistoryItem($item);
        }

        return $this->asJson(
            $response,
            ['items' => $result],
        );
    }
}
