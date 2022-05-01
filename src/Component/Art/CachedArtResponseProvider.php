<?php

namespace Uxmp\Core\Component\Art;

use DateTimeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;
use Uxmp\Core\Component\Config\ConfigProviderInterface;

final class CachedArtResponseProvider implements CachedArtResponseProviderInterface
{
    public function __construct(
        private readonly ConfigProviderInterface $config,
        private readonly Psr17Factory $psr17Factory,
        private readonly ArtContentRetrieverInterface $artContentRetriever,
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

        try {
            $artContent = $this->artContentRetriever->retrieve($item);
        } catch (ArtContentException) {
            return $this->createErrorResponse($response);
        }

        return $this->createResponse(
            $response,
            $artContent,
            sprintf('%s.jpg', $artItemId),
            $item->getLastModified()
        );
    }

    private function createErrorResponse(
        ResponseInterface $response
    ): ResponseInterface {
        return $response
            ->withHeader('Content-Type', 'image/png')
            ->withHeader('Content-Disposition', 'filename=disc.png')
            ->withBody(
                $this->psr17Factory->createStreamFromFile((string) realpath(__DIR__ . '/../../../resource/asset/disc.png'))
            );
    }

    /**
     * @param array{content: string, mimeType: string} $artContent
     */
    private function createResponse(
        ResponseInterface $response,
        array $artContent,
        string $filename,
        ?DateTimeInterface $lastModified = null
    ): ResponseInterface {
        return $response
            ->withHeader('Cache-Control', sprintf('public, max-age=%d', $this->config->getClientCacheMaxAge()))
            ->withHeader('Last-Modified', (string) $lastModified?->format(DATE_RFC7231))
            ->withHeader('Content-Type', $artContent['mimeType'])
            ->withHeader('Content-Disposition', 'filename='.$filename)
            ->withBody(
                $this->psr17Factory->createStream($artContent['content'])
            );
    }
}
