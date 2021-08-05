<?php

declare(strict_types=1);

namespace Usox\Core\Component\Catalog;

use function DI\autowire;

return [
    CatalogScannerInterface::class => autowire(CatalogScanner::class),
    Scanner\ArtistCacheInterface::class => autowire(Scanner\ArtistCache::class),
    Scanner\AlbumCacheInterface::class => autowire(Scanner\AlbumCache::class),
    Scanner\DiscCacheInterface::class => autowire(Scanner\DiscCache::class),
];
