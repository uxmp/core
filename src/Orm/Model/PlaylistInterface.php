<?php

namespace Uxmp\Core\Orm\Model;

interface PlaylistInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): PlaylistInterface;

    public function getOwnerUser(): ?UserInterface;

    public function setOwnerUser(UserInterface $owner_user): PlaylistInterface;
}
