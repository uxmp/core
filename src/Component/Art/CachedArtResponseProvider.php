<?php

namespace Uxmp\Core\Component\Art;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Slim\HttpCache\CacheProvider;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

final class CachedArtResponseProvider implements CachedArtResponseProviderInterface
{
    public function __construct(
        private CacheProvider $cacheProvider,
        private ConfigProviderInterface $config,
        private Psr17Factory $psr17Factory,
    ) {
    }

    public function withCachedArt(
        ResponseInterface $response,
        CachableArtItemInterface $item,
    ): ResponseInterface {
        $artItemId = $item->getArtItemId();

        if ($artItemId === null) {
            return $this->createErrorResponse($response);
        }

        $filename = $artItemId . '.jpg';
        $path = sprintf(
            '%s/%s',
            $this->config->getAssetPath() . '/img/' . $item->getArtItemType(),
            $filename
        );
        $mimeType = 'image/jpg';

        if (!file_exists($path)) {
            return $this->createErrorResponse($response);
        }

        return $this->createResponse(
            $response,
            $path,
            $mimeType,
            $filename,
            $item->getLastModified()?->getTimestamp()
        );
    }

    private function createErrorResponse(
        ResponseInterface $response
    ): ResponseInterface {
        return $this->createResponse(
            $response,
            (string) realpath(__DIR__ . '/../../../resource/asset/disc.png'),
            'image/png',
            'disc.png'
        );
    }

    private function createResponse(
        ResponseInterface $response,
        string $path,
        string $mimeType,
        string $filename,
        ?int $lastModified = null
    ): ResponseInterface {
        return $this->cacheProvider
            ->withEtag($response, md5((string) $lastModified))
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Disposition', 'filename='.$filename)
            ->withBody(
                $this->psr17Factory->createStreamFromFile($path)
            );
    }
}
