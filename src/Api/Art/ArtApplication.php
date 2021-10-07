<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Art;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Art\CachableArtItemInterface;
use Uxmp\Core\Component\Art\CachedArtResponseProviderInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtApplication extends AbstractApiApplication
{
    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private CachedArtResponseProviderInterface $cachedArtResponseProvider,
        private ArtistRepositoryInterface $artistRepository,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $itemId = (int) ($args['id'] ?? 0);

        $item = match ($args['type'] ?? null) {
            default => null,
            'album' => $this->albumRepository->find($itemId),
            'artist' => $this->artistRepository->find($itemId),
        };

        if (!($item instanceof CachableArtItemInterface)) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        return $this->cachedArtResponseProvider->withCachedArt($response, $item);
    }
}
