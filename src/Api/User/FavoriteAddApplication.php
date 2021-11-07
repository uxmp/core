<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Favorite\FavoriteManagerInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Mark a song as favorite
 */
final class FavoriteAddApplication extends AbstractFavoriteApplication
{
    public function __construct(
        SongRepositoryInterface $songRepository,
        private FavoriteManagerInterface $favoriteManager,
    ) {
        parent::__construct(
            $songRepository
        );
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $obj = $this->getItem($request, $args);

        if ($obj === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        return $this->asJson(
            $response,
            [
                'result' => $this->favoriteManager->add($obj, $user->getId())
            ]
        );
    }
}
