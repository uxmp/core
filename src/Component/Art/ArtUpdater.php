<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use Uxmp\Core\Component\Artist\ArtistCoverUpdaterInterface;
use Uxmp\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtUpdater implements ArtUpdaterInterface
{
    public function __construct(
        private ArtistRepositoryInterface $artistRepository,
        private ArtistCoverUpdaterInterface $artistCoverUpdater,
    ) {
    }

    public function update(int $catalogId): void
    {
        // update the artist images
        foreach ($this->artistRepository->findAll() as $artist) {
            $this->artistCoverUpdater->update($artist);
        }
    }
}
