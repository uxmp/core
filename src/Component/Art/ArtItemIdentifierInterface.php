<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

/**
 * Identifies art items by their `art-id` (like artist-16, album-666, ...)
 */
interface ArtItemIdentifierInterface
{
    public function identify(string $item): ?CachableArtItemInterface;
}
