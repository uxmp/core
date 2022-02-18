<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Uxmp\Core\Orm\Model\UserInterface;

interface UserCreatorInterface
{
    /**
     * Creates a user with default settings
     */
    public function create(string $username, string $passwordHash): UserInterface;
}
