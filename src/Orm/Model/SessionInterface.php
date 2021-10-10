<?php

namespace Uxmp\Core\Orm\Model;

interface SessionInterface
{
    public function getId(): int;

    public function getSubject(): string;

    public function setSubject(string $subject): SessionInterface;

    public function getActive(): bool;

    public function setActive(bool $active): SessionInterface;

    public function getUser(): UserInterface;

    public function setUser(UserInterface $user): SessionInterface;
}
