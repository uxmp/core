<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use Usox\HyperSonic\Authentication\AuthenticationProviderInterface;
use Usox\HyperSonic\Authentication\Exception\AbstractAuthenticationException;
use Usox\HyperSonic\Authentication\Exception\AuthenticationFailedException;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

final class AuthenticationProvider implements AuthenticationProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function authByToken(
        string $userName,
        string $token,
        string $salt,
    ): void {
        // TODO: add real auth, not just the username (testing purposes)
        if ($this->userRepository->findOneBy(['name' => $userName]) === null) {
            throw new AuthenticationFailedException();
        }
    }

    public function authByPassword(
        string $userName,
        string $password,
    ): void {
        // TODO: add real auth, not just the username (testing purposes)
        if ($this->userRepository->findOneBy(['name' => $userName]) === null) {
            throw new AuthenticationFailedException();
        }
    }
}
