<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;
use Uxmp\Core\Component\Album\AlbumDeleterInterface;
use Uxmp\Core\Component\Song\SongDeleterInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogCleaner implements CatalogCleanerInterface
{
    public function __construct(
        private CatalogRepositoryInterface $catalogRepository,
        private AlbumRepositoryInterface $albumRepository,
        private AlbumDeleterInterface $albumDeleter,
        private SongRepositoryInterface $songRepository,
        private SongDeleterInterface $songDeleter,
    ) {
    }

    public function clean(Interactor $io, int $catalogId): void
    {
        $catalog = $this->catalogRepository->find($catalogId);
        if ($catalog === null) {
            $io->error(
                sprintf('Catalog `%d` not found', $catalogId),
                true
            );
            return;
        }

        $directory = $catalog->getPath();

        if (!is_dir($directory)) {
            $io->error(
                sprintf('The path `%s` is not accessible', $directory),
                true
            );
            return;
        }

        $io->info('Cleaning catalog', true);

        $songs = $this->songRepository->findBy(['catalog' => $catalog]);

        foreach ($songs as $song) {
            $io->info(
                sprintf('Delete `%s - %s`', $song->getArtist()->getTitle(), $song->getTitle()),
                true
            );

            $this->songDeleter->delete($song);
        }

        // @todo remove orphaned albums and deactivate artists
    }
}
