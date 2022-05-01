<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

/**
 * Utility methods for password verification, hashing, ...
 */
final class PasswordVerificator implements PasswordVerificatorInterface
{
    public const PASSWORD_MIN_LENGTH = 6;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param string $defaultAlgo
     * @param array<string, mixed> $passwordOptions
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly string $defaultAlgo = PASSWORD_DEFAULT,
        private readonly array $passwordOptions = [],
    ) {
    }

    /**
     * Verifies the users' password input
     *
     * Also performs a migration of the password hash if necessary
     *
     * @return bool `True` if validation was successful
     */
    public function verify(
        UserInterface $user,
        string $password
    ): bool {
        $hash = $user->getPassword();

        $result = password_verify($password, $hash);

        if ($result) {
            if (password_needs_rehash($hash, $this->defaultAlgo, $this->passwordOptions)) {
                $user->setPassword(
                    password_hash($password, $this->defaultAlgo, $this->passwordOptions)
                );
                $this->userRepository->save($user);
            }
        }

        return $result;
    }

    /**
     * Hashes a password
     */
    public function hash(string $password): string
    {
        return password_hash($password, $this->defaultAlgo, $this->passwordOptions);
    }
}
