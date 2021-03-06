<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;
use getID3;
use Uxmp\Core\Component\Catalog\Manage\Update\RecursiveFileReaderInterface;
use Uxmp\Core\Component\Catalog\Scanner\DiscCacheInterface;
use Uxmp\Core\Component\Tag\Container\AudioFile;
use Uxmp\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;
use Uxmp\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogUpdater implements CatalogUpdaterInterface
{
    private const SUPPORTED_FORMATS = [
        'mp3',
        'flac',
        'ogg',
        'wav',
    ];

    public function __construct(
        private readonly CatalogRepositoryInterface $catalogRepository,
        private readonly getID3 $id3Analyzer,
        private readonly SongRepositoryInterface $songRepository,
        private readonly ExtractorDeterminatorInterface $extractorDeterminator,
        private readonly DiscCacheInterface $discCache,
        private readonly RecursiveFileReaderInterface $recursiveFileReader,
    ) {
    }

    public function update(Interactor $io, int $catalogId): void
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

        $skipped = [];

        $io->info(sprintf('Updating catalog from `%s`', $directory), true);

        foreach ($this->recursiveFileReader->read($directory) as $filename) {
            $analysisResult = $this->id3Analyzer->analyze($filename);

            $audioFile = (new AudioFile())
                ->setFilename($filename)
                ->setFileSize($analysisResult['filesize'] ?? 0)
            ;

            $fileFormat = $analysisResult['fileformat'] ?? '';

            if (!in_array($fileFormat, self::SUPPORTED_FORMATS, true)) {
                $skipped[] = sprintf(
                    'Skipped `%s`: Unknown fileformat `%s`',
                    $filename,
                    $fileFormat
                );

                $io->error('.');
                continue;
            }

            $extractor = $this->extractorDeterminator->determine($analysisResult['tags']);
            if ($extractor === null) {
                $skipped[] = sprintf(
                    'Skipped `%s`: Meta-Tag extraction failed',
                    $filename,
                );

                $io->error('.');
                continue;
            }

            $audioFile->setMimeType((string) ($analysisResult['mime_type'] ?? ''));

            $extractor->extract(
                $analysisResult['tags'],
                $audioFile
            );

            if ($audioFile->isValid() === false) {
                $skipped[] = sprintf(
                    'Skipped `%s`: Invalid Meta-Tags',
                    $filename,
                );

                $io->error('.');
                continue;
            }

            $io->info('.');

            $song = $this->songRepository->findByMbId($audioFile->getMbid());
            if ($song === null) {
                $song = $this->songRepository->prototype();
            }

            $disc = $this->discCache->retrieve($catalog, $audioFile, $analysisResult);

            $song
                ->setTitle($audioFile->getTitle())
                ->setTrackNumber($audioFile->getTrackNumber())
                ->setDisc($disc)
                ->setArtist($disc->getAlbum()->getArtist())
                ->setFilename($audioFile->getFilename())
                ->setMbid($audioFile->getMbid())
                ->setCatalog($catalog)
                ->setLength((int) round($analysisResult['playtime_seconds']))
                ->setYear($audioFile->getYear())
                ->setMimeType($audioFile->getMimeType())
                ->setFileSize($audioFile->getFileSize())
            ;

            $this->songRepository->save($song);

            $disc->addSong($song);
        }

        if ($skipped !== []) {
            $io->error('The following errors occurred', true);

            foreach ($skipped as $error) {
                $io->error($error, true);
            }
        }

        $io->eol();
    }
}
