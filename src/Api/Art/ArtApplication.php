<?php

declare(strict_types=1);

namespace Usox\Core\Api\Art;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Usox\Core\Api\AbstractApiApplication;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;

final class ArtApplication extends AbstractApiApplication
{
    public function __construct(
        private Psr17Factory $psr17Factory,
        private AlbumRepositoryInterface $albumRepository
    ) {
    }

    /**
     * @param array{id: string, type: string} $args
     */
    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $albumId = $args['id'];

        $filename = $albumId . '.jpg';
        $path = sprintf(
            '%s/%s',
            realpath(__DIR__ . '/../../../../assets/img/album'),
            $filename
        );

        $size = filesize($path);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-Type', 'image/jpg')
            ->withHeader('Content-Disposition', 'filename='.$filename)
            ->withHeader('Content-Length', (string) $size)
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Content-Range', 'bytes '.$size)
            ->withHeader('Accept-Ranges', 'bytes')
            ->withBody(
                $this->psr17Factory->createStreamFromFile($path)
            );
    }
}
