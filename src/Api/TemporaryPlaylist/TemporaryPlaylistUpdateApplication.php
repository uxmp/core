<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\TemporaryPlaylist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

/**
 * Updates the temporary playlist of a user
 */
final class TemporaryPlaylistUpdateApplication extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{
     *  songIds: array<int>,
     *  playlistId: string
     * }> $schemaValidator
     */
    public function __construct(
        private readonly TemporaryPlaylistRepositoryInterface $temporaryPlaylistRepository,
        private readonly SchemaValidatorInterface $schemaValidator,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'TemporaryPlaylistUpdate.json',
        );

        $temporaryPlaylistId = $body['playlistId'];

        // find existing playlist; if not available, create a new one
        $temporaryPlaylist = $this->temporaryPlaylistRepository->findOneBy([
            'owner' => $user,
            'id' => $temporaryPlaylistId,
        ]);
        if ($temporaryPlaylist === null) {
            $temporaryPlaylist = $this->temporaryPlaylistRepository
                ->prototype()
                ->setId($temporaryPlaylistId)
                ->setOwner($user);
        }

        $temporaryPlaylist->updateSongList($body['songIds']);

        $this->temporaryPlaylistRepository->save($temporaryPlaylist);

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
