<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\FeatureSet\V1161\Contract\GetCoverArtDataProviderInterface;
use Uxmp\Core\Component\Art\ArtContentRetrieverInterface;
use Uxmp\Core\Component\Art\ArtItemIdentifierInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;

/**
 * Retrieves the cover art for items which support art
 */
final class CoverArtDataProvider implements GetCoverArtDataProviderInterface
{
    public function __construct(
        private readonly ArtContentRetrieverInterface $artContentRetriever,
        private readonly ArtItemIdentifierInterface $artItemIdentifier,
    ) {
    }

    /**
     * @return array{
     *  contentType: string,
     *  art: string
     * }
     */
    public function getArt(string $coverArtId): array
    {
        $artContent = [];

        $item = $this->artItemIdentifier->identify($coverArtId);

        if ($item !== null) {
            try {
                $artContent = $this->artContentRetriever->retrieve($item);
            } catch (ArtContentException) {
            }
        }

        return [
            'art' => $artContent['content'] ?? '',
            'contentType' => $artContent['mimeType'] ?? '',
        ];
    }
}
