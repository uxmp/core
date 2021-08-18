<?php

namespace Uxmp\Core\Component\Tag\Extractor;

interface ExtractorDeterminatorInterface
{
    /**
     * @param array<mixed> $data
     */
    public function determine(array $data): ?ExtractorInterface;
}
