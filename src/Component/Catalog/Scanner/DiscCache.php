<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Disc\DiscLengthUpdaterInterface;
use Uxmp\Core\Component\Event\EventHandlerInterface;
use Uxmp\Core\Component\Tag\Container\AudioFileInterface;
use Uxmp\Core\Orm\Model\CatalogInterface;
use Uxmp\Core\Orm\Model\DiscInterface;
use Uxmp\Core\Orm\Repository\DiscRepositoryInterface;

final class DiscCache implements DiscCacheInterface
{
    /** @var array<string, DiscInterface> */
    private array $cache = [];

    public function __construct(
        private readonly DiscRepositoryInterface $discRepository,
        private readonly AlbumCacheInterface $albumCache,
        private readonly EventHandlerInterface $eventHandler
    ) {
    }

    public function retrieve(
        CatalogInterface $catalog,
        AudioFileInterface $audioFile,
        array $analysisResult
    ): DiscInterface {
        $discMbId = $audioFile->getDiscMbid();
        $discNumber = $audioFile->getDiscNumber();
        $album = $this->albumCache->retrieve($catalog, $audioFile, $analysisResult);

        $cacheKey = sprintf('%s_%d', $discMbId, $discNumber);

        $disc = $this->cache[$cacheKey] ?? null;
        if ($disc === null) {
            $disc = $this->discRepository->findUniqueDisc($discMbId, $discNumber);
            if ($disc === null) {
                $disc = $this->discRepository->prototype();
            }

            $disc->setMbid($discMbId)
                ->setAlbum($album)
                ->setNumber($discNumber)
            ;

            $this->discRepository->save($disc);

            $this->cache[$cacheKey] = $disc;
        }

        $this->eventHandler->fire(
            static function (ContainerInterface $c) use ($disc): void {
                $c->get(DiscLengthUpdaterInterface::class)->update($disc);
            }
        );

        return $disc;
    }
}
