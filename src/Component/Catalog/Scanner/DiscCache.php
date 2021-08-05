<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog\Scanner;

use Usox\Core\Component\Tag\Container\AudioFileInterface;
use Usox\Core\Orm\Model\DiscInterface;
use Usox\Core\Orm\Repository\DiscRepositoryInterface;

final class DiscCache implements DiscCacheInterface
{
    /** @var array<string, DiscInterface> */
    private array $cache = [];

    public function __construct(
        private DiscRepositoryInterface $discRepository,
        private AlbumCacheInterface $albumCache
    ) {
    }

    public function retrieve(AudioFileInterface $audioFile): DiscInterface
    {
        $discMbId = $audioFile->getDiscMbid();

        $disc = $this->cache[$discMbId] ?? null;
        if ($disc === null) {
            $disc = $this->discRepository->findByMbId($discMbId);
            if ($disc === null) {
                $disc = $this->discRepository->prototype()
                    ->setMbid($discMbId)
                    ->setAlbum($this->albumCache->retrieve($audioFile))
                    ->setNumber($audioFile->getDiscNumber())
                ;

                $this->discRepository->save($disc);
            }

            $this->cache[$discMbId] = $disc;
        }

        return $disc;
    }
}
