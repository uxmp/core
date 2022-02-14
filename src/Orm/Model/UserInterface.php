<?php

namespace Uxmp\Core\Orm\Model;

interface UserInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): UserInterface;

    public function getPassword(): string;

    public function setPassword(string $password): UserInterface;

    /**
     * Returns the language iso2 code
     */
    public function getLanguage(): string;

    public function setLanguage(string $language): UserInterface;
}
