<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog;

use Generator;
use getID3;
use Usox\Core\Component\Tag\Container\AudioFile;
use Usox\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Usox\Core\Orm\Model\AlbumInterface;
use Usox\Core\Orm\Model\ArtistInterface;
use Usox\Core\Orm\Model\DiscInterface;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;
use Usox\Core\Orm\Repository\DiscRepositoryInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogScanner implements CatalogScannerInterface
{
    public function __construct(
        private getID3 $id3Analyzer,
        private ArtistRepositoryInterface $artistRepository,
        private AlbumRepositoryInterface $albumRepository,
        private SongRepositoryInterface $songRepository,
        private ExtractorDeterminatorInterface $extractorDeterminator,
        private DiscRepositoryInterface $discRepository
    ) {
    }

    public function scan(string $directory): void
    {
        /** @var array<string, ArtistInterface> $artists */
        $artists = [];
        /** @var array<string, AlbumInterface> $albums */
        $albums = [];
        /** @var array<string, DiscInterface> $discs */
        $discs = [];

        foreach ($this->search($directory) as $filename) {
            $audioFile = (new AudioFile())->setFilename($filename);

            $analysisResult = $this->id3Analyzer->analyze($filename)['tags'];

            $extractor = $this->extractorDeterminator->determine($analysisResult);
            if ($extractor === null) {
                continue;
            }

            $extractor->extract(
                $analysisResult,
                $audioFile
            );

            $artistMbid = $audioFile->getArtistMbid();
            $albumMbid = $audioFile->getAlbumMbid();
            $discMbId = $audioFile->getDiscMbId();

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

            $disc = $discs[$discMbId] ?? null;
            if ($disc === null) {
                $disc = $this->discRepository->prototype()
                    ->setMbid($discMbId)
                    ->setAlbum($album)
                    ->setNumber($audioFile->getDiscNumber())
                ;

                $this->discRepository->save($disc);

                $discs[$discMbId] = $disc;
            }

            $song = $this->songRepository->prototype()
                ->setTitle($audioFile->getTitle())
                ->setTrackNumber($audioFile->getTrackNumber())
                ->setDisc($disc)
                ->setArtist($artist)
                ->setFilename($audioFile->getFilename())
                ->setMbid($audioFile->getMbid())
            ;

            $this->songRepository->save($song);
        }
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
