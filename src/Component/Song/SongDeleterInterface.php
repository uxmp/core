<?php

namespace Uxmp\Core\Component\Song;

use Uxmp\Core\Orm\Model\SongInterface;

interface SongDeleterInterface
{
    public function delete(SongInterface $song): void;
}
