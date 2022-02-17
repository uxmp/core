<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Session\SessionValidatorMiddleware;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\PlaybackHistoryRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class PlaySongApplication extends AbstractApiApplication
{
    public function __construct(
        private Psr17Factory $psr17Factory,
        private SongRepositoryInterface $songRepository,
        private PlaybackHistoryRepositoryInterface $playbackHistoryRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $songId = (int) $args['id'];

        $song = $this->songRepository->find($songId);

        if ($song === null) {
            throw new \RuntimeException('song not found');
        }

        /** @var UserInterface $user */
        $user = $request->getAttribute(SessionValidatorMiddleware::USER);

        $history = $this->playbackHistoryRepository->prototype()
            ->setUser($user)
            ->setSong($song)
            ->setPlayDate(new \DateTime());

        $this->playbackHistoryRepository->save($history);

        $path = $song->getFilename();

        $size = filesize($path);

        return $response
            ->withHeader('Content-Type', (string) $song->getMimeType())
            ->withHeader('Content-Disposition', 'filename=song'.$songId.'.mp3')
            ->withHeader('Content-Length', (string) $size)
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Content-Range', 'bytes '.$size)
            ->withHeader('Accept-Ranges', 'bytes')
            ->withBody(
                $this->psr17Factory->createStreamFromFile($path)
            );
    }
}
