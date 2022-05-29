<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Api\Lib\Middleware\SessionValidatorMiddleware;
use Uxmp\Core\Api\Lib\SchemaValidatorInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;
use Uxmp\Core\Orm\Repository\TemporaryPlaylistRepositoryInterface;

/**
 * Adds the song to the users' playback history and updates the temporary playlist
 */
final class NowPlayingUpdate extends AbstractApiApplication
{
    /**
     * @param SchemaValidatorInterface<array{
     *  songId: int,
     *  temporaryPlaylist: array{
     *    id: string|null,
     *    offset: int
     *  }
     * }> $schemaValidator
     */
    public function __construct(
        private readonly SchemaValidatorInterface $schemaValidator,
        private readonly TemporaryPlaylistRepositoryInterface $temporaryPlaylistRepository,
        private readonly PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
        private readonly SongRepositoryInterface $songRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $body = $this->schemaValidator->getValidatedBody(
            $request,
            'NowPlayingUpdate.json',
        );
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $temporaryPlaylist = $this->temporaryPlaylistRepository->findOneBy([
            'id' => $body['temporaryPlaylist']['id'],
            'owner' => $user,
        ]);

        if ($temporaryPlaylist !== null) {
            $temporaryPlaylist->setOffset($body['temporaryPlaylist']['offset']);

            $this->temporaryPlaylistRepository->save($temporaryPlaylist);
        }

        $song = $this->songRepository->find($body['songId']);

        if ($song !== null) {
            $history = $this->playbackHistoryRepository->prototype()
                ->setUser($user)
                ->setSong($song)
                ->setPlayDate(new DateTime());

            $this->playbackHistoryRepository->save($history);
        }

        return $this->asJson(
            $response,
            [
                'result' => true,
            ]
        );
    }
}
