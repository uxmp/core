<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
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

        $genres = array_map('ucwords', $genres);

        $albumId = $album->getId();

        $knownGenres = $this->genreMapRepository->findBy([
            'mapped_item_type' => GenreMapEnum::ALBUM,
            'mapped_item_id' => $albumId,
        ]);

        foreach ($knownGenres as $knownGenre) {
            $genreName = $knownGenre->getGenreTitle();
            $key = array_search($genreName, $genres, true);
            if ($key !== false) {
                unset($genres[$key]);
            } else {
                $this->genreMapRepository->delete($knownGenre);
            }
        }

        foreach ($genres as $genreName) {
            $genreName = ucwords($genreName);

            $cachedGenre = $this->genreRepository->findOneBy([
                'title' => $genreName,
            ]);

            if ($cachedGenre === null) {
                $cachedGenre = $this->genreRepository
                    ->prototype()
                    ->setTitle($genreName);

                $this->genreRepository->save($cachedGenre);
            }

            $mappedGenre = $this->genreMapRepository
                ->prototype()
                ->setGenre($cachedGenre)
                ->setMappedItemType(GenreMapEnum::ALBUM)
                ->setMappedItemId($albumId);

            $this->genreMapRepository->save($mappedGenre);
        }
    }
}
