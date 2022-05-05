<?php

namespace Uxmp\Core\Component\Authentication;

use Uxmp\Core\Orm\Model\SessionInterface;

interface SessionManagerInterface
{
    public function lookup(int $sessionId): ?SessionInterface;

    public function logout(int $sessionId): void;

    public function login(
        string $username,
        string $password
    ): ?SessionInterface;
}
