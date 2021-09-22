<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Art;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

final class ArtApplication extends AbstractApiApplication
{
    public function __construct(
        private Psr17Factory $psr17Factory,
        private ConfigProviderInterface $config
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $albumId = $args['id'];
        $type = $args['type'];

        $subPath = match ($type) {
            'album' => 'album',
            default => 'artist',
        };

        $filename = $albumId . '.jpg';
        $path = sprintf(
            '%s/%s',
            realpath($this->config->getAssetPath() . '/img/' . $subPath),
            $filename
        );

        $size = filesize($path);

        return $response
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
