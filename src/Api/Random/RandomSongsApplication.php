<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Random;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class RandomSongsApplication extends AbstractApiApplication
{
    private const DEFAULT_LIMIT = 100;

    public function __construct(
        private SongRepositoryInterface $songRepository,
        private ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        $limit = (int) ($args['limit'] ?? self::DEFAULT_LIMIT);

        foreach ($this->songRepository->findAll() as $song) {
            $album = $song->getDisc()->getAlbum();

            $list[] = $this->resultItemFactory->createSongListItem($song, $album);
        }

        // @todo inefficient, but doctrine doesn't support RAND order natively
        shuffle($list);

        return $this->asJson(
            $response,
            ['items' => array_slice($list, 0, $limit)]
        );
    }
}
