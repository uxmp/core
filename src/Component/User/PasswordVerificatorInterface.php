<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Uxmp\Core\Orm\Model\UserInterface;

interface PasswordVerificatorInterface
{
    /**
     * Verifies the users' password input
     *
     * Also performs a migration of the password hash if necessary
     *
     * @return bool `True` if validation was successful
     */
    public function verify(UserInterface $user, string $password): bool;

    /**
     * Hashes a password
     */
    public function hash(string $password): string;
}
