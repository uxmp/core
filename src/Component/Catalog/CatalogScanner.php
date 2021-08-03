<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog;

use getID3;
use Usox\Core\Component\Tag\Container\IntermediateAlbum;
use Usox\Core\Component\Tag\Container\IntermediateAlbumInterface;
use Usox\Core\Component\Tag\Container\IntermediateArtist;
use Usox\Core\Component\Tag\Container\IntermediateSong;
use Usox\Core\Component\Tag\Extractor\Id3Extractor;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;
use Usox\Core\Orm\Repository\SongRepositoryInterface;

final class CatalogScanner implements CatalogScannerInterface
{
    public function __construct(
        private getID3 $id3Analyzer,
        private ArtistRepositoryInterface $artistRepository,
        private AlbumRepositoryInterface $albumRepository,
        private SongRepositoryInterface $songRepository
    ) {
    }

    public function scan(string $directory): array
    {
        $result = [];

        $this->search($directory, $result);

        /** @var array<IntermediateAlbumInterface> $albums */
        $albums = [];
        $artists = [];

        $extractor = new Id3Extractor();
        foreach ($result as $filename) {
            $song = $extractor->extract(
                $filename,
                $this->id3Analyzer->analyze($filename)['tags']['id3v2']
            );

            $album_key = md5($song['album'].$song['artist']);
            $artist_key = md5($song['artist']);

            if (!array_key_exists($artist_key, $artists)) {
                $artists[$artist_key] = (new IntermediateArtist())
                    ->setTitle($song['artist']);
                ;
            }

            if (!array_key_exists($album_key, $albums)) {
                $albums[$album_key] = (new IntermediateAlbum())
                    ->setTitle($song['album'])
                    ->setArtist($song['artist'])
                ;

                $artists[$artist_key]->addAlbum($albums[$album_key]);
            }
            $albums[$album_key]->addSong(
                (new IntermediateSong())
                    ->setTitle($song['title'])
                    ->setFilename($song['filename'])
                    ->setTrackNumber($song['track'])
            );
        }

        foreach ($artists as $artist_im) {
            $artist = $this->artistRepository->prototype()
                ->setTitle($artist_im->getTitle())
            ;

            $this->artistRepository->save($artist);

            foreach ($artist_im->getAlbums() as $album_im) {
                $album = $this->albumRepository->prototype()
                    ->setTitle($album_im->getTitle())
                    ->setArtist($artist)
                ;

                $this->albumRepository->save($album);

                foreach ($album_im->getSongs() as $song_im) {
                    $song = $this->songRepository->prototype()
                        ->setTitle($song_im->getTitle())
                        ->setTrackNumber($song_im->getTrackNumber())
                        ->setAlbum($album)
                        ->setArtist($artist)
                        ->setFilename($song_im->getFilename())
                    ;

                    $this->songRepository->save($song);
                }
            }
        }

        return $artists;
    }

    private function search(string $directory, array &$result): void
    {
        $files = scandir($directory);

        foreach ($files as $value) {
            $path = realpath($directory . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $result[] = $path;
            } else if ($value != "." && $value != "..") {
                $this->search($path, $result);
            }
        }
    }
}