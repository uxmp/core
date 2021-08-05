<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\AlbumInterface;
use Usox\Core\Orm\Repository\AlbumRepositoryInterface;

final class AlbumCache implements AlbumCacheInterface
{
    /** @var array<string, AlbumInterface> */
    private array $cache = [];

    public function __construct(
        private AlbumRepositoryInterface $albumRepository,
        private ArtistCacheInterface $artistCache
    ) {
    }

    public function retrieve(AudioFileInterface $audioFile): AlbumInterface
    {
        $albumMbid = $audioFile->getAlbumMbid();

        $album = $this->cache[$albumMbid] ?? null;
        if ($album === null) {
            $album = $this->albumRepository->findByMbId($albumMbid);
            if ($album === null) {
                $album = $this->albumRepository->prototype()
                    ->setTitle($audioFile->getAlbumTitle())
                    ->setArtist($this->artistCache->retrieve($audioFile))
                    ->setMbid($albumMbid)
                ;
                $this->albumRepository->save($album);
            }

            $this->cache[$albumMbid] = $album;
        }

        return $album;
    }
}
