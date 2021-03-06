<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use DateTime;
use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Album\AlbumCoverUpdaterInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumCache implements AlbumCacheInterface
{
    /** @var array<string, AlbumInterface> */
    private array $cache = [];

    public function __construct(
        private readonly AlbumRepositoryInterface $albumRepository,
        private readonly ArtistCacheInterface $artistCache,
        private readonly EventHandlerInterface $eventHandler,
        private readonly GenreCacheInterface $genreCache,
    ) {
    }

    public function retrieve(
        CatalogInterface $catalog,
        AudioFileInterface $audioFile,
        array $analysisResult
    ): AlbumInterface {
        $albumMbid = $audioFile->getAlbumMbid();

        $album = $this->cache[$albumMbid] ?? null;
        if ($album === null) {
            $album = $this->albumRepository->findByMbId($albumMbid);
            if ($album === null) {
                $artist = $this->artistCache->retrieve($audioFile);

                $album = $this->albumRepository->prototype()
                    ->setTitle($audioFile->getAlbumTitle())
                    ->setArtist($artist)
                    ->setMbid($albumMbid)
                    ->setCatalog($catalog)
                    ->setLastModified(new DateTime())
                ;
                $this->albumRepository->save($album);

                $artist->addAlbum($album);
            }
            $this->eventHandler->fire(
                static function (ContainerInterface $c) use ($album, $analysisResult): void {
                    $c->get(AlbumCoverUpdaterInterface::class)->update($album, $analysisResult);
                }
            );

            $this->genreCache->enrich($album, $audioFile);

            $this->cache[$albumMbid] = $album;
        }

        return $album;
    }
}
