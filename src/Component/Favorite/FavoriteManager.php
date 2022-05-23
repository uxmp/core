<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Favorite;

use DateTime;
use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\FavoriteRepositoryInterface;

final class FavoriteManager implements FavoriteManagerInterface
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository
    ) {
    }

    public function add(
        FavoriteAbleInterface $obj,
        UserInterface $user,
    ): bool {
        $itemId = $obj->getId();
        $type = $obj->getType();

        $favorite = $this->favoriteRepository->findOneBy([
            'user' => $user,
            'item_id' => $itemId,
            'type' => $type,
        ]);

        if ($favorite === null) {
            $favorite = $this->favoriteRepository->prototype()
                ->setUser($user)
                ->setType($type)
                ->setItemId($itemId)
                ->setDate(new DateTime());

            $this->favoriteRepository->save($favorite);

            return true;
        }

        return false;
    }

    public function remove(
        FavoriteAbleInterface $obj,
        UserInterface $user,
    ): bool {
        $favorite = $this->favoriteRepository->findOneBy([
            'user' => $user,
            'item_id' => $obj->getId(),
            'type' => $obj->getType(),
        ]);

        if ($favorite !== null) {
            $this->favoriteRepository->delete($favorite);

            return true;
        }

        return false;
    }
}
