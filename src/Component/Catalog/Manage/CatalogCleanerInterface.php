<?php

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;

interface CatalogCleanerInterface
{
    public function clean(
        Interactor $io,
        int $catalogId
    ): void;
}
