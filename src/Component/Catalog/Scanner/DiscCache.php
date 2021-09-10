<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Scanner;

use Psr\Container\ContainerInterface;
use Uxmp\Core\Component\Album\AlbumCoverUpdaterInterface;
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
        private DiscRepositoryInterface $discRepository,
        private AlbumCacheInterface $albumCache,
        private EventHandlerInterface $eventHandler
    ) {
    }

    public function retrieve(
        CatalogInterface $catalog,
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
                    ->setAlbum($this->albumCache->retrieve($catalog, $audioFile, $analysisResult))
                    ->setNumber($audioFile->getDiscNumber())
                ;

                $this->discRepository->save($disc);
            }

            $this->cache[$discMbId] = $disc;
        } else {
            $this->albumCache->retrieve($catalog, $audioFile, $analysisResult);
        }

        $this->eventHandler->fire(
            static function (ContainerInterface $c) use ($disc): void {
                $c->get(DiscLengthUpdaterInterface::class)->update($disc);
            }
        );

        return $disc;
    }
}
