<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\Session;

use Uxmp\Core\Component\User\PasswordVerificatorInterface;
use Uxmp\Core\Orm\Model\SessionInterface;
use Uxmp\Core\Orm\Repository\SessionRepositoryInterface;
use Uxmp\Core\Orm\Repository\UserRepositoryInterface;

/**
 * Handles session lookup and login/logout of users
 */
final class SessionManager implements SessionManagerInterface
{
    public function __construct(
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordVerificatorInterface $passwordVerificator,
    ) {
    }

    public function lookup(int $sessionId): ?SessionInterface
    {
        return $this->sessionRepository->find($sessionId);
    }

    public function logout(int $sessionId): void
    {
        $session = $this->lookup($sessionId);
        if ($session !== null) {
            $session->setActive(false);

            $this->sessionRepository->save($session);
        }
    }

    public function login(
        string $username,
        string $password
    ): ?SessionInterface {
        $user = $this->userRepository->findOneBy([
            'name' => $username,
        ]);

        if ($user === null) {
            return null;
        }

        if ($this->passwordVerificator->verify($user, $password) === false) {
            return null;
        }

        $session = $this->sessionRepository
            ->prototype()
            ->setActive(true)
            ->setUser($user);

        $this->sessionRepository->save($session);

        return $session;
    }
}
