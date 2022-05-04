<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Art;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teapot\StatusCode;
use Uxmp\Core\Api\AbstractApiApplication;
use Uxmp\Core\Component\Art\ArtItemIdentifierInterface;
use Uxmp\Core\Component\Art\CachedArtResponseProviderInterface;

final class ArtApplication extends AbstractApiApplication
{
    public function __construct(
        private readonly CachedArtResponseProviderInterface $cachedArtResponseProvider,
        private readonly ArtItemIdentifierInterface $artItemIdentifier,
    ) {
    }

    protected function run(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $item = $this->artItemIdentifier->identify(
            sprintf(
                '%s-%d',
                $args['type'] ?? '',
                $args['id'] ?? 0
            )
        );

        if ($item === null) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        return $this->cachedArtResponseProvider->withCachedArt($response, $item);
    }
}
