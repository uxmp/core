<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Tag;

use Uxmp\Core\Component\Tag\Extractor\ExtractorDeterminator;
use Uxmp\Core\Component\Tag\Extractor\ExtractorDeterminatorInterface;
use Uxmp\Core\Component\Tag\Extractor\Id3v2Extractor;
use Uxmp\Core\Component\Tag\Extractor\VorbisExtractor;
use function DI\autowire;

return [
    ExtractorDeterminatorInterface::class => autowire(ExtractorDeterminator::class)->constructorParameter(
        'availableExtractors',
        [
            autowire(Id3v2Extractor::class),
            autowire(VorbisExtractor::class),
        ]
    ),
];
