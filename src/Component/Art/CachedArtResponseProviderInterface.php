<?php

namespace Uxmp\Core\Component\Art;

use Psr\Http\Message\ResponseInterface;

interface CachedArtResponseProviderInterface
{
    public function withCachedArt(
        ResponseInterface $response,
        CachableArtItemInterface $item,
    ): ResponseInterface;
}
