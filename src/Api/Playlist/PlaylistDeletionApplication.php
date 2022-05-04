<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Component\OwnerValidationTrait;
use Uxmp\Core\Api\Lib\Exception\AccessViolation;
use Uxmp\Core\Orm\Repository\PlaylistRepositoryInterface;

/**
 * Playlist deletion
 */
final class PlaylistDeletionApplication extends AbstractApiApplication
{
    use OwnerValidationTrait;

    public function __construct(
        private readonly PlaylistRepositoryInterface $playlistRepository,
    ) {
    }

    /**
     * @throws AccessViolation
     */
    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $playlistId = (int) ($args['playlistId'] ?? 0);

        $playlist = $this->playlistRepository->find($playlistId);

        $result = false;

        if ($playlist !== null) {
            $this->validateOwner($request, $playlist);

            $this->playlistRepository->delete($playlist);

            $result = true;
        }

        return $this->asJson(
            $response,
            [
                'result' => $result,
            ]
        );
    }
}
