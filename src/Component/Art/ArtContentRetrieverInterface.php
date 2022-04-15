<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Art;

/**
 * Retrieves the binary data of an art item
 */
interface ArtContentRetrieverInterface
{
    /**
     * @return array{
     *  mimeType: string,
     *  content: string
     * }
     *
     * @throws Exception\ArtContentException
     */
    public function retrieve(CachableArtItemInterface $item): array;
}
