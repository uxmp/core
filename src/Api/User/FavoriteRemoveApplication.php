<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Component\Favorite\FavoriteManagerInterface;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Remove a item from the users favorites
 */
final class FavoriteRemoveApplication extends AbstractFavoriteApplication
{
    public function __construct(
        SongRepositoryInterface $songRepository,
        AlbumRepositoryInterface $albumRepository,
        private FavoriteManagerInterface $favoriteManager,
    ) {
        parent::__construct(
            $songRepository,
            $albumRepository,
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
                'result' => $this->favoriteManager->remove($obj, $user)
            ]
        );
    }
}
