<?php

namespace Uxmp\Core\Component\Art;

interface ArtUpdaterInterface
{
    public function update(int $catalogId): void;
}
