<?php

namespace Usox\Core\Component\Catalog;

use Usox\Core\Component\Tag\Container\IntermediateArtistInterface;

interface CatalogScannerInterface
{
    /**
     * @return array<IntermediateArtistInterface>
     */
    public function scan(string $directory): array;
}
