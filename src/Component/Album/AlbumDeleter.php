<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Album;

use Uxmp\Core\Orm\Model\AlbumInterface;

final class AlbumDeleter implements AlbumDeleterInterface
{
    public function delete(AlbumInterface $album): void
    {
    }
}
