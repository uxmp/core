<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Playlist\Smartlist\Type;

use Generator;
use Uxmp\Core\Orm\Model\PlaylistInterface;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

final class FavoriteSongsType implements SmartlistTypeInterface
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository,
    ) {
    }

    /**
     * @return Generator<int>
     */
    public function getSongList(
        PlaylistInterface $playlist,
        UserInterface $user
    ): Generator {
        foreach ($this->favoriteRepository->findBy(['user' => $user, 'type' => 'song']) as $item) {
            yield $item->getItemId();
        }
    }
}
