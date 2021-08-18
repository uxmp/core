<?php

namespace Uxmp\Core\Orm\Model;

interface UserInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): UserInterface;

    public function getPassword(): string;

    public function setPassword(string $password): UserInterface;
}
