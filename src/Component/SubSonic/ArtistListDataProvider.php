<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use DateTimeInterface;
use Generator;
use Usox\HyperSonic\FeatureSet\V1161\Contract\ArtistListDataProviderInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

/**
 * Provides the complete list of available artists
 */
final class ArtistListDataProvider implements ArtistListDataProviderInterface
{
    public function __construct(
        private ArtistRepositoryInterface $artistRepository
    ) {
    }

    /**
     * @return array<string>
     *
     * @todo implement stripping of articles (like 'the, el, los, ...')
     */
    public function getIgnoredArticles(): array
    {
        return [];
    }

    /**
     * @return Generator<array{
     *  id: string,
     *  name: string,
     *  coverArtId: string,
     *  artistImageUrl: string,
     *  albumCount: int,
     *  starred: null|DateTimeInterface
     * }>
     */
    public function getArtists(
        ?string $musicFolderId
    ): Generator {
        foreach ($this->artistRepository->findBy([], ['title' => 'ASC']) as $artist) {
            $artistId = $artist->getId();

            yield [
                'id' => (string) $artistId,
                'name' => (string) $artist->getTitle(),
                'artistImageUrl' => '',
                'coverArtId' => 'artist-'.$artistId,
                'albumCount' => $artist->getAlbumCount(),
                'starred' => null,
            ];
        }
    }
}
