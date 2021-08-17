<?php

namespace Usox\Core\Component\Session;

use Usox\Core\Orm\Model\SessionInterface;

interface SessionManagerInterface
{
    public function lookup(string $subject): ?SessionInterface;

    public function logout(int $sessionId): void;

    public function login(
        string $username,
        string $password
    ): ?SessionInterface;
}