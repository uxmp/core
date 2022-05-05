<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Random;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\ResultItemFactoryInterface;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Builds a result with random favorite songs of a user
 */
final class RandomFavoriteSongsApplication extends AbstractApiApplication
{
    private const DEFAULT_LIMIT = 100;

    public function __construct(
        private readonly SongRepositoryInterface $songRepository,
        private readonly ResultItemFactoryInterface $resultItemFactory,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $list = [];

        $limit = (int) ($args['limit'] ?? self::DEFAULT_LIMIT);

        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        foreach ($this->songRepository->findFavorites($user) as $song) {
            $list[] = $this->resultItemFactory->createSongListItem(
                $song,
                $song->getDisc()->getAlbum()
            );
        }

        // @todo inefficient, but doctrine doesn't support RAND order natively
        shuffle($list);

        return $this->asJson(
            $response,
            ['items' => array_slice($list, 0, $limit)]
        );
    }
}
