<?php

declare(strict_types=1);

namespace Usox\Core\Component\Session;

use Usox\Core\Orm\Model\SessionInterface;
use Usox\Core\Orm\Repository\SessionRepositoryInterface;
use Usox\Core\Orm\Repository\UserRepositoryInterface;

final class SessionManager implements SessionManagerInterface
{
    public function __construct(
        private SessionRepositoryInterface $sessionRepository,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function lookup(string $subject): ?SessionInterface
    {
        return $this->sessionRepository->find((int) $subject);
    }

    public function logout(int $sessionId): void
    {
        $session = $this->sessionRepository->find($sessionId);
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

        if (password_verify($password, $user->getPassword()) === false) {
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
