<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

final class CatalogLister implements CatalogListerInterface
{
    public function __construct(
        private CatalogRepositoryInterface $catalogRepository,
    ) {
    }

    public function list(Interactor $io): void
    {
        $catalogs = $this->catalogRepository->findAll();

        $list = [];

        foreach ($catalogs as $catalog) {
            $list[] = [
                'id' => $catalog->getId(),
                'path' => $catalog->getPath(),
            ];
        }

        $io->table($list);
    }
}
