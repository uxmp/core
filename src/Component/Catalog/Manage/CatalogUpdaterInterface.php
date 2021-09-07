<?php

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;

interface CatalogUpdaterInterface
{
    public function update(
        Interactor $io,
        int $catalogId
    ): void;
}
