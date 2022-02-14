<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\UserRepository")
 * @Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="string")
     */
    private string $name = '';

    /**
     * @Column(type="string", options={"default" : "en"})
     */
    private string $language = 'en';

    /**
     * @Column(type="string")
     */
    private string $password = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): UserInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returns the language iso2 code
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): UserInterface
    {
        $this->language = $language;
        return $this;
    }
}
