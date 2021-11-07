<?php

namespace Uxmp\Core\Orm\Model;

use DateTimeInterface;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\FavoriteRepository")
 * @Table(name="favorite")
 */
interface FavoriteInterface
{
    public function setUser(UserInterface $user): FavoriteInterface;

    public function getUser(): UserInterface;

    public function setItemId(int $itemId): FavoriteInterface;

    public function getItemId(): int;

    public function setType(string $type): FavoriteInterface;

    public function getType(): string;

    public function setDate(DateTimeInterface $date): FavoriteInterface;

    public function getDate(): DateTimeInterface;
}
