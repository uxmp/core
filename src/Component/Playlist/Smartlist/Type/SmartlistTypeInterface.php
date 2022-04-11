<?php

namespace Uxmp\Core\Component\Playlist\Smartlist\Type;

use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;

interface SmartlistTypeInterface
{
    /**
     * @return iterable<int>
     */
    public function getSongList(
        PlaylistInterface $playlist,
        UserInterface $user,
    ): iterable;
}
