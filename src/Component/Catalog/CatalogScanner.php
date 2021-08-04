<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog;

use Generator;
use getID3;
use Usox\Core\Component\Tag\Container\AudioFile;
use Usox\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogScanner implements CatalogScannerInterface
{
    public function __construct(
        private getID3 $id3Analyzer,
        private ArtistRepositoryInterface $artistRepository,
        private AlbumRepositoryInterface $albumRepository,
        private SongRepositoryInterface $songRepository,
        private ExtractorDeterminatorInterface $extractorDeterminator
    ) {
    }

    public function scan(string $directory): array
    {
        $artists = [];
        $albums = [];

        foreach ($this->search($directory) as $filename) {
            $audioFile = new AudioFile();

            $analysisResult = $this->id3Analyzer->analyze($filename)['tags'];

            $extractor = $this->extractorDeterminator->determine($analysisResult);
            if ($extractor === null) {
                continue;
            }

            $extractor->extract(
                $filename,
                $analysisResult,
                $audioFile
            );

            $artistMbid = $audioFile->getArtistMbid();
            $albumMbid = $audioFile->getAlbumMbid();

            $artist = $artists[$artistMbid] ?? null;
            if ($artist === null) {
                $artist = $this->artistRepository->prototype()
                    ->setTitle($audioFile->getArtistTitle())
                    ->setMbid($audioFile->getArtistMbid())
                ;
                $this->artistRepository->save($artist);

                $artists[$artistMbid] = $artist;
            }

            $album = $albums[$albumMbid] ?? null;
            if ($album === null) {
                $album = $this->albumRepository->prototype()
                    ->setTitle($audioFile->getAlbumTitle())
                    ->setArtist($artist)
                    ->setMbid($albumMbid)
                ;
                $this->albumRepository->save($album);

                $albums[$albumMbid] = $album;
            }

            $song = $this->songRepository->prototype()
                ->setTitle($audioFile->getTitle())
                ->setTrackNumber($audioFile->getTrackNumber())
                ->setAlbum($album)
                ->setArtist($artist)
                ->setFilename($audioFile->getFilename())
                ->setMbid($audioFile->getMbid())
            ;

            $this->songRepository->save($song);
        }

        return $artists;
    }

    /**
     * @return Generator<string>
     */
    private function search(string $directory): Generator
    {
        $files = scandir($directory);

        if ($files !== false) {
            foreach ($files as $value) {
                $path = (string) realpath($directory . DIRECTORY_SEPARATOR . $value);
                if (!is_dir($path)) {
                    yield $path;
                } elseif ($value != '.' && $value != '..') {
                    yield from $this->search($path);
                }
            }
        }
    }
}
