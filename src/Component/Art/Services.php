<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

use function DI\autowire;

return [
    CachedArtResponseProviderInterface::class => autowire(CachedArtResponseProvider::class),
    ArtUpdaterInterface::class => autowire(ArtUpdater::class),
    ArtContentRetrieverInterface::class => autowire(ArtContentRetriever::class),
    ArtItemIdentifierInterface::class => autowire(ArtItemIdentifier::class),
];
