<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Generator;
use Usox\HyperSonic\FeatureSet\V1161\Contract\GenreListDataProviderInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

/**
 * Provides the complete list of available genres
 */
final class GenresDataProvider implements GenreListDataProviderInterface
{
    public function __construct(
        private readonly GenreRepositoryInterface $genreRepository
    ) {
    }

    /**
     * @return Generator<array{
     *  value: string,
     *  albumCount: int,
     *  songCount: int
     * }>
     */
    public function getGenres(): Generator
    {
        return $this->genreRepository->getGenreStatistics();
    }
}
