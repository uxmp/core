<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

/**
 * Identifies art items by their `art-id` (like artist-16, album-666, ...)
 */
final class ArtItemIdentifier implements ArtItemIdentifierInterface
{
    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ArtistRepositoryInterface $artistRepository,
    ) {
    }

    public function identify(
        string $item
    ): ?CachableArtItemInterface {
        $data = explode('-', $item);
        if (count($data) !== 2) {
            return null;
        }

        [$itemType, $itemId] = $data;

        return match ($itemType) {
            default => null,
            'album' => $this->albumRepository->find((int) $itemId),
            'artist' => $this->artistRepository->find((int) $itemId),
        };
    }
}
