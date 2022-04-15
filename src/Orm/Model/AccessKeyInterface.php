<?php

namespace Uxmp\Core\Orm\Model;

interface AccessKeyInterface
{
    public function getId(): int;

    public function getTypeId(): int;

    public function setTypeId(int $typeId): AccessKeyInterface;

    public function getActive(): bool;

    public function setActive(bool $active): AccessKeyInterface;

    public function getUser(): UserInterface;

    public function setUser(UserInterface $user): AccessKeyInterface;

    /**
     * @return array{
     *  accessToken?: string
     * }
     */
    public function getConfig(): array;

    /**
     * @param array{
     *  accessToken?: string,
     * } $config
     */
    public function setConfig(array $config): AccessKeyInterface;
}
