<?php

namespace Uxmp\Core\Component\Catalog\Manage;

use Ahc\Cli\IO\Interactor;

interface CatalogListerInterface
{
    public function list(Interactor $io): void;
}
