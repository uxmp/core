<?php

namespace Uxmp\Core\Component\Artist;

use Uxmp\Core\Orm\Model\ArtistInterface;

interface ArtistCoverUpdaterInteface
{
    public function update(ArtistInterface $artist): void;
}
