<?php

namespace Uxmp\Core\Orm\Model;

interface RadioStationInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): static;

    public function getUrl(): string;

    public function setUrl(string $url): static;
}
