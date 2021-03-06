<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

/**
 * Returns the song stream
 */
final class PlaySongApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly Psr17Factory $psr17Factory,
        private readonly SongRepositoryInterface $songRepository,
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
            throw new RuntimeException('song not found');
        }

        $path = $song->getFilename();
        $size = $song->getFileSize();

        return $response
            ->withHeader('Content-Type', (string) $song->getMimeType())
            ->withHeader('Content-Disposition', sprintf('filename=song%d.mp3', $songId))
            ->withHeader('Content-Length', (string) $size)
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Content-Range', 'bytes '.$size)
            ->withHeader('Accept-Ranges', 'bytes')
            ->withBody(
                $this->psr17Factory->createStreamFromFile($path)
            );
    }
}
