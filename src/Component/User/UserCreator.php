<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\User;

use Uxmp\Core\Orm\Model\UserInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

/**
 * Creates a user
 */
final class UserCreator implements UserCreatorInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordVerificatorInterface $passwordVerificator,
    ) {
    }

    /**
     * Creates a user with default settings
     */
    public function create(
        string $username,
        string $password,
    ): UserInterface {
        $user = $this
            ->userRepository->prototype()
            ->setName($username)
            ->setPassword(
                $this->passwordVerificator->hash($password)
            );

        $this->userRepository->save($user);

        return $user;
    }
}
