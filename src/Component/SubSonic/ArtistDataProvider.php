<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use DateTimeInterface;
use Usox\HyperSonic\FeatureSet\V1161\Contract\ArtistDataProviderInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

/**
 * Provides data for a single artist
 */
final class ArtistDataProvider implements ArtistDataProviderInterface
{
    public function __construct(
        private readonly ArtistRepositoryInterface $artistRepository
    ) {
    }

    /**
     * @return null|array{
     *  id: int|string,
     *  name: string,
     *  coverArtId: string,
     *  albumCount: int,
     *  artistImageUrl: string,
     *  albums: array<array{
     *    id: int|string,
     *    name: string,
     *    coverArtId: string,
     *    songCount: int,
     *    createDate: DateTimeInterface|null,
     *    duration: int,
     *    artistName: string,
     *    artistId: int|string,
     *    year?: string,
     *    genre?: string,
     *    playCount?: int
     *  }>
     * }
     */
    public function getArtist(
        string $artistId
    ): ?array {
        $artist = $this->artistRepository->find((int) $artistId);

        if ($artist === null) {
            return null;
        }

        $albums = [];
        $artistName = (string) $artist->getTitle();

        foreach ($artist->getAlbums() as $album) {
            $albumId = $album->getId();

            $albums[] = [
                'id' => $albumId,
                'name' => (string) $album->getTitle(),
                'coverArtId' => 'album-'.$albumId,
                'songCount' => $album->getSongCount(),
                'createDate' => null, // @todo implement,
                'duration' => $album->getLength(),
                'artistName' => $artistName,
                'artistId' => $artistId,
            ];
        }

        return [
            'id' => $artistId,
            'name' => $artistName,
            'coverArtId' => 'artist-'.$artistId,
            'artistImageUrl' => '',
            'albumCount' => $artist->getAlbumCount(),
            'albums' => $albums,
        ];
    }
}
