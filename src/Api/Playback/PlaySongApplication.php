<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Playback;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class PlaySongApplication extends AbstractApiApplication
{
    public function __construct(
        private Psr17Factory $psr17Factory,
        private SongRepositoryInterface $songRepository
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
        $path = $song->getFilename();

        $size = filesize($path);

        return $response
            ->withHeader('Content-Type', 'audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3')
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
