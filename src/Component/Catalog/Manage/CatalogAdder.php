<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;
use Uxmp\Core\Orm\Repository\CatalogRepositoryInterface;

final class CatalogAdder implements CatalogAdderInterface
{
    public function __construct(
        private readonly CatalogRepositoryInterface $catalogRepository
    ) {
    }

    public function add(Interactor $io, string $path): void
    {
        $realPath = realpath($path);

        if ($realPath === false) {
            $io->error(sprintf('`%s` is not a valid path', $path), true);
            return;
        }

        $catalog = $this->catalogRepository->findOneBy(['path' => $realPath]);

        if ($catalog !== null) {
            $io->error(sprintf('`%s` has already been added as a catalog', $realPath), true);
            return;
        }

        $catalog = $this->catalogRepository->prototype();
        $catalog->setPath($realPath);

        $this->catalogRepository->save($catalog);

        $io->ok(sprintf('Catalog has been added with id `%d`', $catalog->getId()), true);
    }
}
