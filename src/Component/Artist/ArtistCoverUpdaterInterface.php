<?php

namespace Uxmp\Core\Component\Artist;

use Uxmp\Core\Orm\Model\ArtistInterface;

interface ArtistCoverUpdaterInterface
{
    public function update(ArtistInterface $artist): void;
}
