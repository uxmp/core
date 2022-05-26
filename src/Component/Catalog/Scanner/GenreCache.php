<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\GenreInterface;
use Uxmp\Core\Orm\Model\GenreMapEnum;
use Uxmp\Core\Orm\Repository\GenreMapRepositoryInterface;
use Uxmp\Core\Orm\Repository\GenreRepositoryInterface;

final class GenreCache implements GenreCacheInterface
{
    public function __construct(
        private readonly GenreRepositoryInterface $genreRepository,
        private readonly GenreMapRepositoryInterface $genreMapRepository,
    ) {
    }

    public function enrich(
        AlbumInterface $album,
        AudioFileInterface $audioFile,
    ): void {
        $genres = $audioFile->getGenres();

        if ($genres === []) {
            return;
        }

        foreach ($genres as $genre_name) {
            $cachedGenre = $this->genreRepository->findOneBy([
                    'title' => $genre_name,
                ]);

            if ($cachedGenre === null) {
                $cachedGenre = $this->genreRepository
                        ->prototype()
                        ->setTitle($genre_name);

                $this->genreRepository->save($cachedGenre);
            }

            $mapped_genre = $this->genreMapRepository
                ->prototype()
                ->setGenre($cachedGenre)
                ->setMappedItemType(GenreMapEnum::ALBUM)
                ->setMappedItemId($album->getId());

            $this->genreMapRepository->save($mapped_genre);
        }
    }
}
