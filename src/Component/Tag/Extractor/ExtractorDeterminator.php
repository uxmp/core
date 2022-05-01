<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag\Extractor;

final class ExtractorDeterminator implements ExtractorDeterminatorInterface
{
    /**
     * @param array<ExtractorInterface> $availableExtractors
     */
    public function __construct(
        private readonly array $availableExtractors
    ) {
    }

    public function determine(
        array $data
    ): ?ExtractorInterface {
        foreach ($this->availableExtractors as $extractor) {
            if ($extractor->applies($data)) {
                return $extractor;
            }
        }

        return null;
    }
}
