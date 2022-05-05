<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Authentication\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

/**
 * Returns three dictionaries containing information on a users favorites.
 */
final class FavoriteListApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $favorites = $this->favoriteRepository->findBy([
            'user' => $user,
        ]);

        $result = [
            'album' => [],
            'song' => [],
            'artist' => [],
        ];

        foreach ($favorites as $favorite) {
            $result[$favorite->getType()][$favorite->getItemId()] = $favorite->getDate()->getTimestamp();
        }

        return $this->asJson(
            $response,
            $result
        );
    }
}
