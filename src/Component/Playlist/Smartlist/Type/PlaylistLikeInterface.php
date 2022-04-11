<?php

namespace Uxmp\Core\Component\Playlist\Smartlist\Type;

interface PlaylistLikeInterface
{
    /**
     * @return iterable<int>
     */
    public function getSongList(): iterable;
}
