<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;
use Uxmp\Core\Component\Album\AlbumDeleterInterface;
use Uxmp\Core\Component\Song\SongDeleterInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogCleaner implements CatalogCleanerInterface
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalogRepository,
        private readonly AlbumDeleterInterface $albumDeleter,
        private readonly SongRepositoryInterface $songRepository,
        private readonly SongDeleterInterface $songDeleter,
        private readonly DiscRepositoryInterface $discRepository,
        private readonly AlbumRepositoryInterface $albumRepository,
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
            if (!file_exists($song->getFilename())) {
                $io->info(
                    sprintf('Delete `%s - %s`', $song->getArtist()->getTitle(), $song->getTitle()),
                    true
                );

                $this->songDeleter->delete($song);
            }
        }

        $discs = $this->discRepository->findEmptyDiscs($catalog);

        foreach ($discs as $disc) {
            $album = $disc->getAlbum();

            $io->info(
                sprintf('Delete disc number `%d` of `%s`', $disc->getNumber(), $album->getTitle()),
                true
            );

            $this->discRepository->delete($disc);

            if ($album->getDiscCount() === 0) {
                $this->deleteAlbum($album, $io);
            }
        }

        $albums = $this->albumRepository->findEmptyAlbums($catalog);

        foreach ($albums as $album) {
            $this->deleteAlbum($album, $io);
        }

        $io->ok('Done', true);
    }

    private function deleteAlbum(AlbumInterface $album, Interactor $io): void
    {
        $io->info(
            sprintf('Delete orphaned album `%s`', $album->getTitle()),
            true
        );

        $this->albumDeleter->delete($album);
    }
}
