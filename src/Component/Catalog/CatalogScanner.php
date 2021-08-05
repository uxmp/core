<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog;

use Generator;
use getID3;
use Usox\Core\Component\Catalog\Scanner\ArtistCacheInterface;
use Usox\Core\Component\Catalog\Scanner\DiscCacheInterface;
use Usox\Core\Component\Tag\Container\AudioFile;
use Usox\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogScanner implements CatalogScannerInterface
{
    public function __construct(
        private getID3 $id3Analyzer,
        private SongRepositoryInterface $songRepository,
        private ExtractorDeterminatorInterface $extractorDeterminator,
        private ArtistCacheInterface $artistCache,
        private DiscCacheInterface $discCache
    ) {
    }

    public function scan(string $directory): void
    {
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

            if ($audioFile->isValid() === false) {
                continue;
            }

            $song = $this->songRepository->findByMbId($audioFile->getMbid());
            if ($song === null) {
                $song = $this->songRepository->prototype();
            }

            $song
                ->setTitle($audioFile->getTitle())
                ->setTrackNumber($audioFile->getTrackNumber())
                ->setDisc($this->discCache->retrieve($audioFile))
                ->setArtist($this->artistCache->retrieve($audioFile))
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
