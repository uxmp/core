<?php

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;

interface CatalogAdderInterface
{
    public function add(
        Interactor $io,
        string $path
    ): void;
}
