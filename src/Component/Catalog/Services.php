<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Catalog;

use function DI\autowire;

return [
    Scanner\ArtistCacheInterface::class => autowire(Scanner\ArtistCache::class),
    Scanner\AlbumCacheInterface::class => autowire(Scanner\AlbumCache::class),
    Scanner\DiscCacheInterface::class => autowire(Scanner\DiscCache::class),
    Manage\CatalogAdderInterface::class => autowire(Manage\CatalogAdder::class),
    Manage\CatalogUpdaterInterface::class => autowire(Manage\CatalogUpdater::class),
    Manage\CatalogCleanerInterface::class => autowire(Manage\CatalogCleaner::class),
];
