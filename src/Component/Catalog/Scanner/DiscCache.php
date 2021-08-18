<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;

final class DiscCache implements DiscCacheInterface
{
    /** @var array<string, DiscInterface> */
    private array $cache = [];

    public function __construct(
        private DiscRepositoryInterface $discRepository,
        private AlbumCacheInterface $albumCache
    ) {
    }

    public function retrieve(
        AudioFileInterface $audioFile,
        array $analysisResult
    ): DiscInterface {
        $discMbId = $audioFile->getDiscMbid();

        $disc = $this->cache[$discMbId] ?? null;
        if ($disc === null) {
            $disc = $this->discRepository->findByMbId($discMbId);
            if ($disc === null) {
                $disc = $this->discRepository->prototype()
                    ->setMbid($discMbId)
                    ->setAlbum($this->albumCache->retrieve($audioFile, $analysisResult))
                    ->setNumber($audioFile->getDiscNumber())
                ;

                $this->discRepository->save($disc);
            }

            $this->cache[$discMbId] = $disc;
        } else {
            $this->albumCache->retrieve($audioFile, $analysisResult);
        }

        return $disc;
    }
}
