<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\ArtistInterface;
use Usox\Core\Orm\Repository\ArtistRepositoryInterface;

final class ArtistCache implements ArtistCacheInterface
{
    /** @var array<string, ArtistInterface> */
    private array $cache = [];

    public function __construct(
        private ArtistRepositoryInterface $artistRepository
    ) {
    }

    public function retrieve(AudioFileInterface $audioFile): ArtistInterface
    {
        $artistMbid = $audioFile->getArtistMbid();

        $artist = $this->cache[$artistMbid] ?? null;
        if ($artist === null) {
            $artist = $this->artistRepository->findByMbId($artistMbid);
            if ($artist === null) {
                $artist = $this->artistRepository->prototype()
                    ->setTitle($audioFile->getArtistTitle())
                    ->setMbid($artistMbid)
                ;
                $this->artistRepository->save($artist);
            }

            $this->cache[$artistMbid] = $artist;
        }

        return $artist;
    }
}
