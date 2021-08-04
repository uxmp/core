<?php

namespace Usox\Core\Component\Catalog;

interface CatalogScannerInterface
{
    public function scan(string $directory): void;
}
