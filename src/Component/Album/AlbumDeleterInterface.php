<?php

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Orm\Model\AlbumInterface;

interface AlbumDeleterInterface
{
    public function delete(AlbumInterface $album): void;
}
