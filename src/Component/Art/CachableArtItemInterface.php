<?php

namespace Uxmp\Core\Component\Art;

use DateTimeInterface;

interface CachableArtItemInterface
{
    public function getArtItemType(): string;

    public function getArtItemId(): ?string;

    public function getLastModified(): ?DateTimeInterface;
}
