<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\FeatureSet\V1161\Contract\GetCoverArtDataProviderInterface;
use Uxmp\Core\Component\Art\ArtContentRetrieverInterface;
use Uxmp\Core\Component\Art\Exception\ArtContentException;
use Uxmp\Core\Orm\Model\ArtistInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

/**
 * Retrieves the cover art for items which support art
 */
final class CoverArtDataProvider implements GetCoverArtDataProviderInterface
{
    public function __construct(
        private readonly ArtistRepositoryInterface $artistRepository,
        private readonly ArtContentRetrieverInterface $artContentRetriever,
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
        // @todo Currently artists only; Merge with Api\Art\ArtApplication
        $str = explode('-', $coverArtId);

        /** @var ArtistInterface $artist */
        $artist = $this->artistRepository->find((int) $str[1]);

        try {
            $artContent = $this->artContentRetriever->retrieve($artist);
        } catch (ArtContentException) {
            $artContent = [];
        }

        return [
            'art' => $artContent['content'] ?? '',
            'contentType' => $artContent['mimeType'] ?? '',
        ];
    }
}
