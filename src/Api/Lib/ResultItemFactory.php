<?php

declare(strict_types=1);

namespace Uxmp\Core\Api\Lib;

use Uxmp\Core\Component\Config\ConfigProviderInterface;
use Uxmp\Core\Orm\Model\AlbumInterface;
use Uxmp\Core\Orm\Model\SongInterface;

final class ResultItemFactory implements ResultItemFactoryInterface
{
    public function __construct(
        private ConfigProviderInterface $config
    ) {
    }

    public function createSongListItem(
        SongInterface $song,
        AlbumInterface $album
    ): SongListItemInterface {
        return new SongListItem(
            $this->config,
            $song,
            $album
        );
    }
}
