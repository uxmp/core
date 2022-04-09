<?php

namespace Uxmp\Core\Orm\Model;

use Uxmp\Core\Component\User\OwnerProviderInterface;

interface PlaylistInterface extends OwnerProviderInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): PlaylistInterface;

    public function setOwner(UserInterface $owner): PlaylistInterface;
}
